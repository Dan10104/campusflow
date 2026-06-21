<template>
  <div class="dropdown me-4">
    <a type="button" id="dropdownThemes" data-bs-toggle="dropdown" aria-expanded="false">
        <img width="35" height="35" src="https://img.icons8.com/fluency/48/themes.png" alt="themes"/>
    </a>
    <ul class="dropdown-menu" aria-labelledby="dropdownThemes">
        <li>
            <a class="dropdown-item" id="btnDefaultTheme" style="cursor: pointer">
                <img width="30" height="30" src="/assets/icons/icons8red.png" alt="roller-brush--v1"/>Tema por defecto
            </a>
        </li>
        <li>
            <a class="dropdown-item" id="btnChildTheme" style="cursor: pointer">
                <img width="30" height="30" src="/assets/icons/icons8blue.png" alt="roller-brush--v1"/>Tema niño
            </a>
        </li>
        <li>
            <a class="dropdown-item" id="btnOldTheme" style="cursor: pointer">
                <img width="30" height="30" src="/assets/icons/icons8gray.png" alt="roller-brush--v1"/>Tema adulto
            </a>
        </li>
    </ul>
</div>
</template>

<script setup>
import { onMounted } from 'vue'

onMounted(() => {
    const saved = localStorage.getItem('theme') || 'default'
  document.body.classList.remove('nino','adulto')
  if (saved !== 'default') document.body.classList.add(saved)
const btnDefaultTheme = document.getElementById("btnDefaultTheme");
        const btnChildTheme = document.getElementById("btnChildTheme");
        const btnOldTheme = document.getElementById("btnOldTheme");

        const app = document.getElementById("app-2");

        let currentTheme = localStorage.getItem("theme");

        const colorDefaultSidenav = "rgb(30 64 175)";
        const colorNinoSidenav = "#0D47A1";
        const colorAdultoSidenav = "#37474F";

        if(currentTheme && currentTheme != "default") {
            if(currentTheme == "adulto"){
                app.classList.add("adulto");
                setThemeSidenav("adulto", true);
                setThemeShowData("adulto", true);

                localStorage.setItem("theme", "adulto");
            }else{
                app.classList.add("nino");
                setThemeSidenav("nino", true);
                setThemeShowData("nino", true);

                localStorage.setItem("theme", "nino");
            }
        }

        btnDefaultTheme.addEventListener("click", () => {
            app.classList.remove("adulto");
            setThemeSidenav("adulto", false);
            setThemeShowData("adulto", false);

            app.classList.remove("nino");
            setThemeSidenav("nino", false);
            setThemeShowData("nino", false);

            localStorage.setItem("theme", "default");
            localStorage.setItem("sidenav", colorDefaultSidenav);
        });

        btnChildTheme.addEventListener("click", () => {
            app.classList.remove("adulto");
            setThemeSidenav("adulto", false);
            setThemeShowData("adulto", false);

            app.classList.add("nino");
            setThemeSidenav("nino", true);
            setThemeShowData("nino", true);

            localStorage.setItem("theme", "nino");
            localStorage.setItem("sidenav", colorNinoSidenav);
        })

        btnOldTheme.addEventListener("click", () => {
            app.classList.remove("nino");
            setThemeSidenav("nino", false);
            setThemeShowData("nino", false);

            app.classList.add("adulto");
            setThemeSidenav("adulto", true);
            setThemeShowData("adulto", true);

            localStorage.setItem("theme", "adulto");
            localStorage.setItem("sidenav", colorAdultoSidenav);
        })


        function setThemeShowData(valueTheme, isCurrentTheme) {
            const formGroupTitle = document.getElementsByClassName("form-group__title");
            const formGroupDescription = document.getElementsByClassName("form-group__description");
            
            setClassToElement(formGroupTitle, isCurrentTheme, valueTheme);
            setClassToElement(formGroupDescription, isCurrentTheme, valueTheme);
        }

        function setThemeSidenav(valueTheme, isCurrentTheme){
            const nav = document.getElementsByClassName("nav");
            const navBody = document.getElementsByClassName("nav__body");
            const navBodyItems = document.getElementsByClassName("nav__body__items");
            const navBodyItemsActive = document.getElementsByClassName("nav__body__items__active");

            setClassToElement(nav, isCurrentTheme, valueTheme);
            setClassToElement(navBody, isCurrentTheme, valueTheme);
            setClassToElement(navBodyItemsActive, isCurrentTheme, valueTheme);

            for(let item of navBodyItems) {
                let hrefs = item.querySelectorAll("a");
                for(let href of hrefs) {
                    isCurrentTheme ? href.classList.add(valueTheme)
                                : href.classList.remove(valueTheme);
                }
            }
        }

        function setClassToElement(element, isAdd, value) {
            for(let item of element) {
                isAdd ? item.classList.add(value)
                                : item.classList.remove(value);
            }
        }
});
</script>
