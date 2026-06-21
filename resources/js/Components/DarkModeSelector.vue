<template>
<div class="dropdown me-4">
    <i class="btn btn-outline-secondary bi bi-moon-stars d-flex justify-content-center" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false" style="width: 30px; height: 30px;"></i>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
        <li>
            <a class="dropdown-item" id="btnOscuro" style="cursor: pointer">
                <i class="bi bi-moon-stars-fill text-secondary me-2"></i>Modo oscuro
            </a>
        </li>
        <li>
            <a class="dropdown-item" id="btnDia" style="cursor: pointer">
                <i class="bi bi-highlights text-secondary me-2"></i>Modo día
            </a>
        </li>
        <li>
            <a class="dropdown-item" id="btnAuto" style="cursor: pointer">
                <i class="bi bi-toggles text-secondary me-2"></i>Modo automático
            </a>
        </li>
    </ul>
</div>
</template>

<script setup>
import { onMounted } from 'vue'

onMounted(() => {
const btnOscuro = document.getElementById("btnOscuro");
        const btnDia = document.getElementById("btnDia");
        const btnAuto = document.getElementById("btnAuto");

        var isDarkTheme = JSON.parse(localStorage.getItem("dark-theme"));
        var isDarkThemeAuto = JSON.parse(localStorage.getItem("dark-theme-auto"));

        const darkColor = "#3c3c3c";
        const lightColor = "#fefefe";

        setInterval(() => {
            if(isDarkThemeAuto) {
                checkThemeAuto();
            }
        }, 4000)

        if (isDarkTheme) {
            loadDarkTheme(isDarkTheme);
        }

        btnOscuro.addEventListener("click", () => {
            loadDarkTheme(true);
            localStorage.setItem("dark-theme", true);
            localStorage.setItem("dark-theme-auto", false);
        })

        btnDia.addEventListener("click", () => {
            loadDarkTheme(false);
            localStorage.setItem("dark-theme", false);
            localStorage.setItem("dark-theme-auto", false);
        })

        btnAuto.addEventListener("click", () => {
            localStorage.setItem("dark-theme-auto", true);
            let dateNow = new Date();
            let hours = dateNow.getHours();
            if (hours >= 18 || hours <= 6) { //De noche
                loadDarkTheme(true);
                localStorage.setItem("dark-theme", true);
            } else { //De día
                loadDarkTheme(false);
                localStorage.setItem("dark-theme", false);
            }
        })

        function checkThemeAuto() {
            let dateNow = new Date();
            let hours = dateNow.getHours();
            if (hours >= 18 || hours <= 6) { //De noche
                if(!isDarkTheme){
                    loadDarkTheme(true);
                    localStorage.setItem("dark-theme", true);
                    isDarkTheme = JSON.parse(localStorage.getItem("dark-theme"));
                }
            } else { //De día
                if(isDarkTheme){
                    loadDarkTheme(false);
                    localStorage.setItem("dark-theme", false);
                    isDarkTheme = JSON.parse(localStorage.getItem("dark-theme"));
                }
            }
        }


        function loadDarkTheme(isDarkMode) {
            if(isDarkMode){
                document.getElementById("app-body").classList.add("bg-dark");
                document.getElementById("app-body").classList.add("text-light");
            }else{
                document.getElementById("app-body").classList.remove("bg-dark");
                document.getElementById("app-body").classList.remove("text-light");
            }
            const wrapper = document.getElementById("app-wrapper");
            if (wrapper) {
                if (isDarkMode) {
                    wrapper.classList.add("bg-dark");
                } else {
                    wrapper.classList.remove("bg-dark");
                }
            }
            
            setBGColorTable(isDarkMode);
            setBGColorShadow(isDarkMode);
            setBGShowData(isDarkMode);
            setBGModalContent(isDarkMode);
            setBGColorBtnClose(isDarkMode);
            setBGColorCards(isDarkMode);
            setBGColorHeader(isDarkMode);
            setBGColorSidenav(isDarkMode);
            setBGColorFooter(isDarkMode);
        }

        function setBGColorTable(isDarkMode) {
            const tables = document.getElementsByClassName("table");
            for(let table of tables){
                if(isDarkMode)
                    table.classList.add("table-dark");
                else
                    table.classList.remove("table-dark");
            }
        }

        function setBGColorShadow(isDarkMode) {
            const shadows = document.getElementsByClassName("shadow")
            for(let shadow of shadows){
                if(isDarkMode)
                    shadow.style.cssText = `background-color: ${darkColor}`;
                else
                    shadow.removeAttribute("style");
            }
        }

        function setBGShowData(isDarkMode) {
            const formGroupDescriptions = document.getElementsByClassName("form-group__description");
            for(let formGroupDescription of formGroupDescriptions){
                if(isDarkMode)
                    formGroupDescription.style.cssText = `
                                            color: ${lightColor};
                                            box-shadow: 0 0 4px ${lightColor};
                                        `;
                else
                    formGroupDescription.removeAttribute("style");
            }
        }

        function setBGModalContent(isDarkMode) {
            const modalContents = document.getElementsByClassName("modal-content");
            for (let modalContent of modalContents){
                if(isDarkMode)
                    modalContent.classList.add("bg-dark", "text-light");
                else
                    modalContent.classList.remove("bg-dark", "text-light");
            }
        }

        function setBGColorBtnClose(isDarkMode) {
            const btnsClose = document.getElementsByClassName("btn-close");
            for (let btnClose of btnsClose){
                if(isDarkMode)
                    btnClose.classList.add("btn-close-white");
                else
                    btnClose.classList.remove("btn-close-white");
            }
        }

        function setBGColorCards(isDarkMode) {
            const cards = document.getElementsByClassName("card");
            for (let card of cards){
                if(isDarkMode)
                    card.classList.add("bg-dark", "text-light");
                else
                    card.classList.remove("bg-dark", "text-light");
            }
        }

        function setBGColorHeader(isDarkMode) {
            const appHeader = document.getElementsByClassName("app__header");
            for (let appHead of appHeader){
                if(isDarkMode){
                    appHead.style.cssText = `
                            border-bottom: 1px solid ${darkColor};
                            background-color: ${darkColor};
                        `;
                }
                else{
                    appHead.removeAttribute("style");
                }
            }
        }

        function setBGColorSidenav(isDarkMode) {
            const navContainer = document.getElementsByClassName("nav");
            for (let nav of navContainer){
                if(isDarkMode){
                    nav.style.cssText = `
                            background-color: ${darkColor};
                            color: ${lightColor};
                        `;
                }else{
                    nav.removeAttribute("style");
                }
            }

            const navBodyItemContainer = document.getElementsByClassName("nav__body__items");
            for(let navBodyItem of navBodyItemContainer){
                let hrefs = navBodyItem.querySelectorAll("a");
                for(let href of hrefs){
                    if(isDarkMode){
                        href.style.cssText = `
                            color: ${lightColor};
                        `;
                    }else{
                        href.removeAttribute("style");
                    }
                }
            }
        }

        function setBGColorFooter(isDarkMode) {
            const appBodyFooter = document.getElementsByClassName("app__body__footer");

            for(let item of appBodyFooter){
                if(isDarkMode){
                    item.style.cssText = `
                        border-top: 1px solid ${darkColor};
                        background-color: ${darkColor};
                    `;
                }else{
                    item.removeAttribute("style");
                }
            }
        }
});
</script>
