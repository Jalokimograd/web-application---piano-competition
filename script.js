const wrapper = document.querySelector('.wrapper');
const loginLink = document.querySelector('.login-link');
const registerLink = document.querySelector('.register-link');
const btnPopup = document.querySelector('.btnLogin-popup');
const iconClose = document.getElementById('LoginPopup-close');

const DBSchemaWrapper = document.getElementById('DBSchema-popup');
const btnDBSchemaPopup = document.getElementById('btnDBSchema-popup');
const iconDBSchemaClose = document.getElementById('DBSchemaPopup-close');

let actualOpenPopup = null;



registerLink.addEventListener('click', () => {
    wrapper.classList.add('active');
});

loginLink.addEventListener('click', () => {
    wrapper.classList.remove('active');
});

btnPopup.addEventListener('click', () => {
    closeOthersPopus(wrapper.classList);

    wrapper.classList.add('active-popup');
});

iconClose.addEventListener('click', () => {
    wrapper.classList.remove('active-popup');

    actualOpenPopup = null;
});

btnDBSchemaPopup.addEventListener('click', () => {
    closeOthersPopus(DBSchemaWrapper.classList);

    DBSchemaWrapper.classList.add('active-popup');
});

iconDBSchemaClose.addEventListener('click', () => {
    DBSchemaWrapper.classList.remove('active-popup');

    actualOpenPopup = null;
});

function closeOthersPopus(newPopus) {
    if (actualOpenPopup != null) {
        actualOpenPopup.remove('active-popup');
    }
    actualOpenPopup = newPopus;
}
