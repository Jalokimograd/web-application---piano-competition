
CREATE TABLE kompozytorzy (
  Id SERIAL PRIMARY KEY,
  Imie varchar(50) DEFAULT NULL,
  Nazwisko varchar(50) DEFAULT NULL
);

INSERT INTO kompozytorzy (Imie, Nazwisko) VALUES
('Fryderyk', 'Chopin'),
('Piotr', 'Czajkowski'),
('Antonin', 'Dvorak'),
('Jacek', 'Kaczmarski');


CREATE TABLE pianisci (
  Id SERIAL PRIMARY KEY,
  Imie varchar(50) DEFAULT NULL,
  Nazwisko varchar(50) DEFAULT NULL,
  username varchar(50) NOT NULL,
  password varchar(50) NOT NULL,
  password_hash varchar(60) NOT NULL,
  email varchar(50) NOT NULL
);


INSERT INTO pianisci (Imie, Nazwisko, username, password, password_hash, email) VALUES
('Adam', 'Małysz', 'Adam', 'Małamysz', '$2y$10$cfXJUmZjU9Xkv/pMzwcovuAdJucEJV4leMAcQbxnZmel60FLm/NV2', 'małamysz@gmail.com'),
('Czesław', 'Małamyszyński', 'czeslaw', 'zaq', '$2y$10$sDQqhM2RKnrYlG3b3lnV0uvP/wvCYeBpovursMtvnsZ/zwkIOAFPW', 'czeslaw@gmail.com'),
('Sułtan', 'Kosmitów', 'otyly_pan', 'zxcv', '$2y$10$ixvtmfYTeF53JCtOyZrm/OLEPeAX8kezD4T.rX.gQDdCmJLQkpVPS', 'otyly_pan@o2.pl'),


CREATE TABLE pracownicy (
  Id SERIAL PRIMARY KEY,
  Imie varchar(50) DEFAULT NULL,
  Nazwisko varchar(50) DEFAULT NULL,
  username varchar(50) NOT NULL,
  password varchar(50) NOT NULL,
  password_hash varchar(60) NOT NULL,
  email varchar(50) NOT NULL
);

INSERT INTO pracownicy (Imie, Nazwisko, username, password, password_hash, email) VALUES
('Admin', 'Adminowski', 'Admin', 'zaq', '$2y$10$sDQqhM2RKnrYlG3b3lnV0uvP/wvCYeBpovursMtvnsZ/zwkIOAFPW', 'admin@gmail.com'),
('Barbara', 'Barbara', 'Pani Basia', 'zaq', '$2y$10$sDQqhM2RKnrYlG3b3lnV0uvP/wvCYeBpovursMtvnsZ/zwkIOAFPW', 'pani_b@gmail.com');


CREATE TABLE utwory (
  Id SERIAL PRIMARY KEY,
  Tytul varchar(50) NOT NULL,
  Kompozytor_Id INTEGER NOT NULL,
  FOREIGN KEY (Kompozytor_Id) REFERENCES kompozytorzy(id)
);

INSERT INTO utwory (Tytul, Kompozytor_Id) VALUES
('Obława I', 4),
('Epitafium dla Włodzimierza Wysockiego', 4),
('Allegro con Fuoco', 3),
('Etiuda Rewolucyjna', 1),
('Jezioro Łabędzie', 2);

CREATE TABLE wykonania (
  Id SERIAL PRIMARY KEY,
  Ocena NUMERIC(2, 1) DEFAULT NULL,
  Zaakceptowany BOOLEAN DEFAULT FALSE,
  Wykonany BOOLEAN DEFAULT FALSE,
  Harmonogram INTEGER DEFAULT NULL,
  Data_zgloszenia TIMESTAMP NOT NULL DEFAULT NOW(),
  Pianisci_Id INTEGER NOT NULL,
  Utwory_Id INTEGER NOT NULL,
  FOREIGN KEY (Pianisci_Id) REFERENCES pianisci(id),
  FOREIGN KEY (Utwory_Id) REFERENCES utwory(id)
);

ALTER TABLE wykonania
ADD CONSTRAINT Ocena CHECK (Ocena >= 0 AND ocena <= 6);

CREATE TABLE etapy_konkursu (
  Id INTEGER PRIMARY KEY,
  nazwa varchar(50) NOT NULL
);

INSERT INTO etapy_konkursu (Id, nazwa) VALUES 
    (1, 'Stage 1: registration of submissions'),
    (2, 'Stage 2: verification of submissions'),
    (3, 'Stage 3: performed and evaluation of compositions'),
    (4, 'Stage 4: competition concluded');

CREATE TABLE konkurs (
  Id SERIAL PRIMARY KEY,
  Data_zakonczenia TIMESTAMP DEFAULT NULL,
  Etap_id INTEGER NOT NULL
  FOREIGN KEY (Etap_id) REFERENCES etapy_konkursu(id)
);

INSERT INTO konkurs (Etap_id) VALUES
(1);

-- sprawdzanie czy zgłoszenia są otwarte
CREATE OR REPLACE FUNCTION aktualizuj_czy_zakonczony() RETURNS TRIGGER AS $$
BEGIN
  IF CURRENT_TIMESTAMP > (SELECT data_zakonczenia FROM konkurs LIMIT 1) THEN 
    UPDATE konkurs SET etap_id = 2;
	  RETURN NULL;
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER sprawdz_data_zakonczenia
BEFORE INSERT ON wykonania
FOR EACH ROW
EXECUTE FUNCTION aktualizuj_czy_zakonczony();

-- sprawdzenie czy nowo dodawany utwór należy do innego kompozytora niż dotychcza
-- dozwolone utwory są filtrowane od strony klienta a od strony serwera dodatkowo sprawdzane
CREATE OR REPLACE FUNCTION sprawdz_czy_rozni_kompozytorzy() RETURNS TRIGGER AS $$
BEGIN
  IF NEW.Utwory_Id IN (	SELECT u.id as u_id
	FROM utwory as u JOIN kompozytorzy as k ON u.kompozytor_id=k.id 
	WHERE k.id NOT IN (SELECT u.kompozytor_id FROM wykonania as w JOIN utwory as u ON w.utwory_id=u.id WHERE w.pianisci_id = NEW.pianisci_id)) THEN
	RETURN NEW;
  END IF;
  RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER sprawdz_czy_rozni_kompozytorzy
BEFORE INSERT ON wykonania
FOR EACH ROW
EXECUTE FUNCTION sprawdz_czy_rozni_kompozytorzy();


-- Funkcja do generownaia harmonogramu
CREATE OR REPLACE FUNCTION ustaw_harmonogram() RETURNS VOID AS $$
DECLARE
	total_rows INT;
	
BEGIN
	SELECT COUNT(*) INTO total_rows FROM wykonania WHERE zaakceptowany=true;

    UPDATE wykonania
    SET harmonogram = CASE
		WHEN (subquery.rn % 3) = 1 THEN (subquery.rn/3 + 1)
		WHEN (subquery.rn % 3) = 2 THEN (subquery.rn/3 + 1) + total_rows/3
		WHEN (subquery.rn % 3) = 0 THEN (subquery.rn/3) + 2*total_rows/3
	END
    FROM (
        SELECT id, pianisci_id, ROW_NUMBER() OVER (ORDER BY pianisci_id) AS rn
        FROM wykonania
		WHERE zaakceptowany=true
    ) AS subquery
    WHERE wykonania.id = subquery.id;
END;
$$ LANGUAGE plpgsql;

-- Trigger sprawdzający czy poprzedni utwór został już oceniony by móc ocenić kolejny
CREATE OR REPLACE FUNCTION czy_poprzednie_wykonanie_ocenione()
RETURNS TRIGGER AS $$

BEGIN
	IF (((SELECT etap_id FROM konkurs) = 3) AND NEW.harmonogram <> 1 AND (SELECT ocena FROM wykonania WHERE harmonogram = (NEW.harmonogram-1)) IS NULL) THEN
		RETURN NULL;
	END IF;
	RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER czy_poprzednie_wykonanie_ocenione
BEFORE UPDATE ON wykonania
FOR EACH ROW
EXECUTE FUNCTION czy_poprzednie_wykonanie_ocenione();


-- Trigger sprawdzający czy wszystkie utowry zostały ocenione i automatycznie przełącza na 4. etap konkursu
CREATE OR REPLACE FUNCTION czy_wszystkie_wykonania_ocenione()
RETURNS TRIGGER AS $$
BEGIN
    IF (SELECT COUNT(*) FROM wykonania WHERE zaakceptowany = TRUE) = (SELECT COUNT(*) FROM wykonania WHERE ocena IS NOT NULL AND zaakceptowany = TRUE) THEN
        UPDATE konkurs SET etap_id = 4;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER czy_wszystkie_wykonania_ocenione
AFTER UPDATE ON wykonania
FOR EACH ROW
EXECUTE FUNCTION czy_wszystkie_wykonania_ocenione();


-- Widok do wyświetlania zbiorczej oceny pianistów
CREATE VIEW ocena_pianistow AS
	SELECT pianisci_id as id, p.imie as pianista_imie, p.nazwisko as pianista_nazwisko, SUM(w.ocena) as ocena_calkowita 
  FROM wykonania as w 
  JOIN pianisci as p ON w.pianisci_id = p.id 
  WHERE zaakceptowany = TRUE AND ocena IS NOT NULL 
  GROUP BY pianisci_id, p.imie, p.nazwisko 
  ORDER BY ocena_calkowita DESC;
