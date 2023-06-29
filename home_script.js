const btns_names = [];
btns_names.push({ popup: "performances", open_buttons: ["btnPerformances-popup"] });
btns_names.push({ popup: "composers", open_buttons: ["btnComposers-popup"] });
btns_names.push({ popup: "songs", open_buttons: ["btnSongs-popup"] });
btns_names.push({ popup: "myPerformances", open_buttons: ["btnMyPerformances-popup"] });
btns_names.push({ popup: "settings", open_buttons: ["btnSettings-popup"] });



let actualOpenPopup = null;

function closeOthersPopus(newPopus) {
    if (actualOpenPopup != null) {
        actualOpenPopup.classList.remove('active-popup');
    }
    actualOpenPopup = newPopus;
}

btns_names.forEach(item => {
    // bierzyemy uchwyt do wyskakującego okienka
    const actualPopup = document.getElementById(item["popup"]);

    if (actualPopup == null)
        return;

    // bierzyemy uchwyt do zagnieżdżonej w nim przycisku zamknięcia
    const buttonClose = actualPopup.querySelector('.icon-close');

    if (buttonClose == null)
        return;

    item["open_buttons"].forEach(buttonId => {
        const button = document.getElementById(buttonId);

        button.addEventListener('click', () => {
            closeOthersPopus(actualPopup);
            actualPopup.classList.add('active-popup');
        });
    })

    buttonClose.addEventListener('click', () => {
        actualPopup.classList.remove('active-popup');

        actualOpenPopup = null;
    });
})

const loginLink = document.querySelector('.login-link');
const registerLink = document.querySelector('.register-link');


const modes_names = [];
modes_names.push({ popup: "songs", change_to_mode1_button: ".addNewSong-link", change_to_mode2_button: ".listOfSongs-link" });
modes_names.push({ popup: "composers", change_to_mode1_button: ".addNewComposer-link", change_to_mode2_button: ".listOfComposers-link" });
modes_names.push({ popup: "myPerformances", change_to_mode1_button: ".addNewPerformance-link", change_to_mode2_button: ".listOfMyPerformances-link" });

modes_names.forEach(item => {
    // bierzyemy uchwyt do wyskakującego okienka
    const actualPopup = document.getElementById(item["popup"]);
    if (actualPopup == null)
        return;

    const mode1Button = actualPopup.querySelector(item["change_to_mode1_button"]);
    const mode2Button = actualPopup.querySelector(item["change_to_mode2_button"]);

    if (mode1Button == null || mode2Button == null)
        return;

    mode1Button.addEventListener('click', () => {
        actualPopup.classList.add('active');
    });

    mode2Button.addEventListener('click', () => {
        actualPopup.classList.remove('active');
    });
})
