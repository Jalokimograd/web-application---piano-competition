const btns_names = [];
btns_names.push({ popup: "performances", open_buttons: ["btnPerformances-popup"] });
btns_names.push({ popup: "composers", open_buttons: ["btnComposers-popup"] });
btns_names.push({ popup: "songs", open_buttons: ["btnSongs-popup"] });
btns_names.push({ popup: "myPerformances", open_buttons: ["btnMyPerformances-popup"] });
btns_names.push({ popup: "settings", open_buttons: ["btnSettings-popup"] });
btns_names.push({ popup: "submissions", open_buttons: ["btnSubmissions-popup"] });
btns_names.push({ popup: "pianistsScore", open_buttons: ["btnPianistsScore-popup"] });


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
modes_names.push({ popup: "songs", change_mode_button: "addNewSong-link", return_button: "listOfSongs-link", mode_name: "active" });
modes_names.push({ popup: "composers", change_mode_button: "addNewComposer-link", return_button: "listOfComposers-link", mode_name: "active" });
modes_names.push({ popup: "myPerformances", change_mode_button: "addNewPerformance-link", return_button: "listOfMyPerformances-link", mode_name: "active" });
modes_names.push({ popup: "myPerformances", change_mode_button: "deletePerformance-link", return_button: "listOfMyPerformances2-link", mode_name: "active2" });
modes_names.push({ popup: "settings", change_mode_button: "changeSettings-link", return_button: "viewSettings-link", mode_name: "active" });

modes_names.forEach(item => {
    // bierzyemy uchwyt do wyskakującego okienka
    const actualPopup = document.getElementById(item["popup"]);
    if (actualPopup == null)
        return;

    const change_mode_button = document.getElementById(item["change_mode_button"]);
    const return_button = document.getElementById(item["return_button"]);
    const modeName = item["mode_name"];

    if (change_mode_button == null || return_button == null)
        return;

    change_mode_button.addEventListener('click', () => {
        actualPopup.classList.add(modeName);
    });

    return_button.addEventListener('click', () => {
        actualPopup.classList.remove(modeName);
    });
})
