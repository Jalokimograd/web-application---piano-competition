const btns_names = [];
btns_names.push({ popup: "results", open_buttons: ["btnResults-popup"] });
btns_names.push({ popup: "schedule", open_buttons: ["btnSchedule-popup"] });
btns_names.push({ popup: "composers", open_buttons: ["btnComposers-popup"] });
btns_names.push({ popup: "songs", open_buttons: ["btnSongs-popup"] });
btns_names.push({ popup: "myPerformance", open_buttons: ["btnMyPerformance-popup"] });



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

