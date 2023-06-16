-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 16 Cze 2023, 07:43
-- Wersja serwera: 10.4.17-MariaDB
-- Wersja PHP: 8.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `konkurs_pianistyczny`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kompozytorzy`
--

CREATE TABLE `kompozytorzy` (
  `Id` int(11) NOT NULL,
  `Imie` varchar(50) DEFAULT NULL,
  `Nazwisko` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `kompozytorzy`
--

INSERT INTO `kompozytorzy` (`Id`, `Imie`, `Nazwisko`) VALUES
(1, 'Fryderyk', 'Chopin'),
(3, 'Piotr', 'Czajkowski'),
(4, 'Antonin', 'Dvorak'),
(5, 'Jacek', 'Kaczmarski');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pianisci`
--

CREATE TABLE `pianisci` (
  `Id` int(11) NOT NULL,
  `Imie` varchar(50) NOT NULL,
  `Nazwisko` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `password_hash` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `pianisci`
--

INSERT INTO `pianisci` (`Id`, `Imie`, `Nazwisko`, `username`, `password`, `password_hash`) VALUES
(2, 'Adam', 'Małysz', 'Adam', 'Małamysz', '$2y$10$cfXJUmZjU9Xkv/pMzwcovuAdJucEJV4leMAcQbxnZmel60FLm/NV2');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownicy`
--

CREATE TABLE `pracownicy` (
  `Id` int(11) NOT NULL,
  `Imie` varchar(50) NOT NULL,
  `Nazwisko` varchar(50) NOT NULL,
  `Login` varchar(50) NOT NULL,
  `Haslo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `utwory`
--

CREATE TABLE `utwory` (
  `Id` int(11) NOT NULL,
  `Tytul` varchar(50) NOT NULL,
  `Kompozytor_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `utwory`
--

INSERT INTO `utwory` (`Id`, `Tytul`, `Kompozytor_Id`) VALUES
(1, 'Obława I', 5),
(2, 'Epitafium dla Włodzimierza Wysockiego', 5),
(5, 'Allegro con Fuoco', 4),
(6, 'Etiuda Rewolucyjna', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wykonania`
--

CREATE TABLE `wykonania` (
  `Id` int(11) NOT NULL,
  `Ocena` int(11) DEFAULT NULL,
  `Pianisci_Id` int(11) NOT NULL,
  `Utwory_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `kompozytorzy`
--
ALTER TABLE `kompozytorzy`
  ADD PRIMARY KEY (`Id`);

--
-- Indeksy dla tabeli `pianisci`
--
ALTER TABLE `pianisci`
  ADD PRIMARY KEY (`Id`);

--
-- Indeksy dla tabeli `pracownicy`
--
ALTER TABLE `pracownicy`
  ADD PRIMARY KEY (`Id`);

--
-- Indeksy dla tabeli `utwory`
--
ALTER TABLE `utwory`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Kompozytor_Id` (`Kompozytor_Id`);

--
-- Indeksy dla tabeli `wykonania`
--
ALTER TABLE `wykonania`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Utwory_Id` (`Utwory_Id`),
  ADD KEY `Pianisci_Id` (`Pianisci_Id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `kompozytorzy`
--
ALTER TABLE `kompozytorzy`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT dla tabeli `pianisci`
--
ALTER TABLE `pianisci`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT dla tabeli `pracownicy`
--
ALTER TABLE `pracownicy`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `utwory`
--
ALTER TABLE `utwory`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT dla tabeli `wykonania`
--
ALTER TABLE `wykonania`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `utwory`
--
ALTER TABLE `utwory`
  ADD CONSTRAINT `utwory_ibfk_1` FOREIGN KEY (`Kompozytor_Id`) REFERENCES `kompozytorzy` (`Id`);

--
-- Ograniczenia dla tabeli `wykonania`
--
ALTER TABLE `wykonania`
  ADD CONSTRAINT `wykonania_ibfk_1` FOREIGN KEY (`Utwory_Id`) REFERENCES `utwory` (`Id`),
  ADD CONSTRAINT `wykonania_ibfk_2` FOREIGN KEY (`Pianisci_Id`) REFERENCES `pianisci` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
