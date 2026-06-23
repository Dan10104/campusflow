<script setup>
import HeaderGuest from '@/Components/HeaderGuest.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
  authShell: {
    type: Boolean,
    default: false,
  },
});

const benefits = [
  {
    title: 'Eficiencia',
    description: 'Optimiza el uso de espacios y recursos.',
  },
  {
    title: 'Transparencia',
    description: 'Información centralizada y trazable.',
  },
  {
    title: 'Seguridad',
    description: 'Acceso protegido según roles.',
  },
];

const floatingCards = ['Reservas', 'Activos', 'Reportes', 'Usuarios'];
</script>

<template>
  <div id="app-2" class="app app-2 app--guest default">
    <HeaderGuest />

    <main class="app__body app__body--guest" id="app-body">
      <section v-if="authShell" class="auth-shell" aria-label="Autenticación CampusFlow">
        <aside class="auth-shell__visual" aria-label="CampusFlow por Nexora Tech">
          <div class="auth-shell__orb auth-shell__orb--one"></div>
          <div class="auth-shell__orb auth-shell__orb--two"></div>
          <div class="auth-shell__mesh"></div>

          <div class="auth-shell__visual-content">
            <div class="auth-shell__brand-large">
              <ApplicationLogo class="auth-shell__brand-logo" />
              <div>
                <p class="auth-shell__brand-name">CampusFlow</p>
                <p class="auth-shell__brand-company">por Nexora Tech</p>
              </div>
            </div>

            <div class="auth-shell__headline">
              <p>Gestión inteligente de espacios y activos</p>
              <h1>Una plataforma universitaria conectada, clara y segura.</h1>
            </div>
          </div>

          <div class="auth-shell__illustration" aria-hidden="true">
            <svg class="auth-shell__svg" viewBox="0 0 760 520" role="presentation" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="auth-campus-building" x1="150" y1="118" x2="510" y2="430" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#ffffff" stop-opacity="0.96" />
                  <stop offset="1" stop-color="#BEEBFF" stop-opacity="0.88" />
                </linearGradient>
                <linearGradient id="auth-campus-accent" x1="210" y1="120" x2="650" y2="400" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#06B6D4" />
                  <stop offset="1" stop-color="#60A5FA" />
                </linearGradient>
              </defs>

              <path d="M80 425C136 351 190 333 270 365C338 392 378 438 460 407C538 378 568 322 650 345C701 360 727 398 741 425H80Z" fill="#ffffff" opacity="0.11" />
              <path d="M128 430H638" stroke="#ffffff" stroke-opacity="0.36" stroke-width="4" stroke-linecap="round" />

              <g class="auth-shell__tower">
                <rect x="506" y="110" width="74" height="270" rx="18" fill="url(#auth-campus-building)" />
                <path d="M514 109L544 62L574 109H514Z" fill="#DFF7FF" />
                <rect x="532" y="78" width="24" height="24" rx="12" fill="#1E40AF" opacity="0.72" />
                <rect x="529" y="142" width="30" height="48" rx="10" fill="#2563EB" opacity="0.22" />
                <rect x="529" y="212" width="30" height="48" rx="10" fill="#2563EB" opacity="0.22" />
                <rect x="529" y="282" width="30" height="48" rx="10" fill="#2563EB" opacity="0.22" />
              </g>

              <g class="auth-shell__building">
                <path d="M164 198L326 108L488 198H164Z" fill="#DFF7FF" />
                <rect x="190" y="198" width="272" height="182" rx="22" fill="url(#auth-campus-building)" />
                <rect x="222" y="224" width="44" height="116" rx="12" fill="#1E40AF" opacity="0.2" />
                <rect x="304" y="224" width="44" height="116" rx="12" fill="#1E40AF" opacity="0.2" />
                <rect x="386" y="224" width="44" height="116" rx="12" fill="#1E40AF" opacity="0.2" />
                <rect x="248" y="358" width="156" height="22" rx="10" fill="#0B1F3A" opacity="0.26" />
                <path d="M326 138L356 196H296L326 138Z" fill="url(#auth-campus-accent)" opacity="0.9" />
              </g>

              <g class="auth-shell__student">
                <circle cx="506" cy="324" r="28" fill="#F8FAFC" />
                <path d="M474 386C481 351 493 336 510 336C529 336 543 353 552 386H474Z" fill="#06B6D4" />
                <path d="M448 386H590C598 386 604 392 604 400V410H434V400C434 392 440 386 448 386Z" fill="#0B1F3A" opacity="0.82" />
                <rect x="464" y="352" width="100" height="56" rx="10" fill="#E0F2FE" />
                <path d="M482 379H546" stroke="#2563EB" stroke-width="6" stroke-linecap="round" opacity="0.62" />
                <path d="M460 396H568" stroke="#ffffff" stroke-width="5" stroke-linecap="round" opacity="0.7" />
              </g>

              <g opacity="0.8">
                <circle cx="132" cy="150" r="6" fill="#06B6D4" />
                <circle cx="620" cy="168" r="8" fill="#ffffff" opacity="0.7" />
                <circle cx="660" cy="292" r="5" fill="#06B6D4" />
                <path d="M110 250H148M129 231V269" stroke="#ffffff" stroke-opacity="0.45" stroke-width="4" stroke-linecap="round" />
              </g>
            </svg>

            <div
              v-for="(card, index) in floatingCards"
              :key="card"
              class="auth-shell__floating-card"
              :class="`auth-shell__floating-card--${index + 1}`"
            >
              <span></span>
              {{ card }}
            </div>
          </div>

          <div class="auth-shell__benefits">
            <div v-for="benefit in benefits" :key="benefit.title" class="auth-shell__benefit">
              <strong>{{ benefit.title }}</strong>
              <span>{{ benefit.description }}</span>
            </div>
          </div>
        </aside>

        <section class="auth-shell__right">
          <div class="auth-shell__right-orb auth-shell__right-orb--one"></div>
          <div class="auth-shell__right-orb auth-shell__right-orb--two"></div>

          <div class="auth-shell__mobile-brand">
            <ApplicationLogo class="auth-shell__mobile-logo" />
            <div>
              <p>CampusFlow</p>
              <span>Gestión inteligente de espacios y activos</span>
            </div>
          </div>

          <div class="auth-shell__card">
            <div class="auth-shell__card-brand">
              <ApplicationLogo class="auth-shell__card-logo" />
              <div>
                <p>CampusFlow</p>
                <span>por Nexora Tech</span>
              </div>
            </div>

            <slot />
          </div>
        </section>
      </section>

      <slot v-else />
    </main>
  </div>
</template>

<style scoped>
.auth-shell {
  --cf-auth-header-height: 4.75rem;
  display: grid;
  min-height: calc(100vh - var(--cf-auth-header-height));
  grid-template-columns: minmax(0, 52fr) minmax(0, 48fr);
  overflow: visible;
  background: #f8fafc;
}

.auth-shell__visual {
  position: relative;
  display: flex;
  min-height: calc(100vh - var(--cf-auth-header-height));
  flex-direction: column;
  justify-content: space-between;
  overflow: hidden;
  padding: clamp(2rem, 5vw, 4.75rem);
  color: #fff;
  background:
    radial-gradient(circle at 15% 15%, rgb(6 182 212 / 34%), transparent 28rem),
    radial-gradient(circle at 82% 20%, rgb(255 255 255 / 18%), transparent 24rem),
    linear-gradient(135deg, #2563eb 0%, #1e40af 42%, #0b1f3a 100%);
}

.auth-shell__mesh {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgb(255 255 255 / 9%) 1px, transparent 1px),
    linear-gradient(90deg, rgb(255 255 255 / 9%) 1px, transparent 1px);
  background-size: 34px 34px;
  mask-image: linear-gradient(135deg, #000 0%, transparent 82%);
}

.auth-shell__orb,
.auth-shell__right-orb {
  position: absolute;
  border-radius: 999px;
  pointer-events: none;
}

.auth-shell__orb--one {
  width: 18rem;
  height: 18rem;
  right: -5rem;
  top: -4rem;
  border: 1px solid rgb(255 255 255 / 22%);
}

.auth-shell__orb--two {
  width: 12rem;
  height: 12rem;
  left: -3rem;
  bottom: 18%;
  background: rgb(255 255 255 / 8%);
}

.auth-shell__visual-content,
.auth-shell__illustration,
.auth-shell__benefits {
  position: relative;
  z-index: 1;
}

.auth-shell__brand-large,
.auth-shell__card-brand,
.auth-shell__mobile-brand {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.auth-shell__brand-logo {
  width: 4.25rem;
  height: 4.25rem;
  flex: none;
  filter: drop-shadow(0 18px 24px rgb(0 0 0 / 24%));
}

.auth-shell__brand-name {
  margin: 0;
  font-size: clamp(2rem, 5vw, 4rem);
  font-weight: 900;
  line-height: 0.95;
  letter-spacing: 0;
}

.auth-shell__brand-company {
  margin: 0.55rem 0 0;
  color: rgb(255 255 255 / 72%);
  font-size: 1rem;
  font-weight: 700;
}

.auth-shell__headline {
  margin-top: clamp(2rem, 4vw, 4rem);
  max-width: 42rem;
}

.auth-shell__headline p {
  margin: 0;
  color: #a7f3ff;
  font-size: clamp(1.15rem, 1.8vw, 1.6rem);
  font-weight: 900;
}

.auth-shell__headline h1 {
  margin: 1rem 0 0;
  max-width: 39rem;
  color: #fff;
  font-size: clamp(2.25rem, 4vw, 4.6rem);
  font-weight: 900;
  line-height: 0.98;
  letter-spacing: 0;
}

.auth-shell__illustration {
  width: min(100%, 48rem);
  margin: clamp(1.5rem, 4vh, 3rem) auto 0;
}

.auth-shell__svg {
  display: block;
  width: 100%;
  height: auto;
  filter: drop-shadow(0 28px 34px rgb(0 0 0 / 20%));
}

.auth-shell__building,
.auth-shell__tower,
.auth-shell__student,
.auth-shell__floating-card {
  animation: auth-float 7s ease-in-out infinite;
}

.auth-shell__tower {
  animation-delay: -2s;
}

.auth-shell__student {
  animation-delay: -4s;
}

.auth-shell__floating-card {
  position: absolute;
  display: flex;
  align-items: center;
  gap: 0.55rem;
  min-width: 8.25rem;
  border: 1px solid rgb(255 255 255 / 22%);
  border-radius: 1rem;
  padding: 0.8rem 1rem;
  color: #fff;
  background: rgb(11 31 58 / 52%);
  box-shadow: 0 18px 36px rgb(0 0 0 / 18%);
  font-size: 0.9rem;
  font-weight: 900;
  backdrop-filter: blur(16px);
}

.auth-shell__floating-card span {
  width: 0.62rem;
  height: 0.62rem;
  flex: none;
  border-radius: 999px;
  background: #06b6d4;
  box-shadow: 0 0 0 5px rgb(6 182 212 / 18%);
}

.auth-shell__floating-card--1 {
  left: 5%;
  top: 31%;
}

.auth-shell__floating-card--2 {
  right: 6%;
  top: 24%;
  animation-delay: -1.5s;
}

.auth-shell__floating-card--3 {
  left: 9%;
  bottom: 18%;
  animation-delay: -3s;
}

.auth-shell__floating-card--4 {
  right: 9%;
  bottom: 23%;
  animation-delay: -4.5s;
}

.auth-shell__benefits {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.9rem;
  margin-top: clamp(1.5rem, 4vh, 3rem);
}

.auth-shell__benefit {
  min-width: 0;
  border: 1px solid rgb(255 255 255 / 16%);
  border-radius: 1rem;
  padding: 1rem;
  background: rgb(255 255 255 / 10%);
  backdrop-filter: blur(14px);
}

.auth-shell__benefit strong,
.auth-shell__benefit span {
  display: block;
}

.auth-shell__benefit strong {
  color: #fff;
  font-size: 0.95rem;
  font-weight: 900;
}

.auth-shell__benefit span {
  margin-top: 0.35rem;
  color: rgb(255 255 255 / 72%);
  font-size: 0.8rem;
  line-height: 1.45;
}

.auth-shell__right {
  position: relative;
  display: flex;
  min-height: calc(100vh - var(--cf-auth-header-height));
  align-items: center;
  justify-content: center;
  overflow: hidden;
  padding: clamp(1.25rem, 4vw, 4rem);
  background:
    radial-gradient(circle at 18% 18%, rgb(37 99 235 / 10%), transparent 18rem),
    radial-gradient(circle at 86% 84%, rgb(6 182 212 / 12%), transparent 20rem),
    #f8fafc;
}

.auth-shell__right-orb--one {
  width: 14rem;
  height: 14rem;
  right: -5rem;
  top: 9%;
  border: 1px solid rgb(37 99 235 / 12%);
}

.auth-shell__right-orb--two {
  width: 8rem;
  height: 8rem;
  left: 8%;
  bottom: 10%;
  background: rgb(6 182 212 / 9%);
}

.auth-shell__mobile-brand {
  display: none;
}

.auth-shell__card {
  position: relative;
  z-index: 1;
  width: min(100%, 520px);
  border: 1px solid var(--cf-border);
  border-radius: 1.5rem;
  background: color-mix(in srgb, var(--cf-surface) 96%, transparent);
  box-shadow: 0 28px 72px rgb(15 23 42 / 14%);
  padding: clamp(1.35rem, 3vw, 2.35rem);
  backdrop-filter: blur(18px);
}

.auth-shell__card-brand {
  margin-bottom: 1.75rem;
}

.auth-shell__card-logo,
.auth-shell__mobile-logo {
  width: 3rem;
  height: 3rem;
  flex: none;
}

.auth-shell__card-brand p,
.auth-shell__mobile-brand p {
  margin: 0;
  color: var(--cf-heading);
  font-size: 1.25rem;
  font-weight: 900;
  line-height: 1;
}

.auth-shell__card-brand span,
.auth-shell__mobile-brand span {
  display: block;
  margin-top: 0.35rem;
  color: var(--cf-text-muted);
  font-size: 0.84rem;
  font-weight: 800;
}

:global(:root[data-theme='dark']) .auth-shell {
  background: var(--cf-bg);
}

:global(:root[data-theme='dark']) .auth-shell__right {
  background:
    radial-gradient(circle at 18% 18%, rgb(96 165 250 / 16%), transparent 18rem),
    radial-gradient(circle at 86% 84%, rgb(6 182 212 / 12%), transparent 20rem),
    var(--cf-bg);
}

:global(:root[data-theme='dark']) .auth-shell__card {
  background: color-mix(in srgb, var(--cf-surface) 94%, transparent);
  box-shadow: 0 28px 72px rgb(0 0 0 / 30%);
}

@keyframes auth-float {
  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-10px);
  }
}

@media (max-width: 1023px) {
  .auth-shell {
    display: block;
    min-height: calc(100vh - var(--cf-auth-header-height));
    overflow: visible;
  }

  .auth-shell__visual {
    display: none;
  }

  .auth-shell__right {
    min-height: calc(100vh - var(--cf-auth-header-height));
    align-items: flex-start;
    overflow: visible;
    padding: 1rem;
  }

  .auth-shell__mobile-brand {
    position: relative;
    z-index: 1;
    display: flex;
    width: min(100%, 520px);
    margin: 0 auto 1rem;
    border: 1px solid var(--cf-border);
    border-radius: 1.25rem;
    padding: 1rem;
    background: color-mix(in srgb, var(--cf-surface) 90%, transparent);
    box-shadow: var(--cf-shadow-sm);
  }

  .auth-shell__card {
    margin: 0 auto;
    border-radius: 1.25rem;
  }

  .auth-shell__card-brand {
    display: none;
  }
}

@media (min-width: 1024px) {
  .auth-shell {
    height: calc(100vh - var(--cf-auth-header-height));
    min-height: 0;
    overflow: hidden;
  }

  .auth-shell__visual {
    display: grid;
    height: 100%;
    min-height: 0;
    grid-template-rows: auto minmax(0, 1fr) auto;
    padding: clamp(1.5rem, 3.2vw, 3.25rem);
  }

  .auth-shell__brand-logo {
    width: clamp(3rem, 4.4vw, 3.8rem);
    height: clamp(3rem, 4.4vw, 3.8rem);
  }

  .auth-shell__brand-name {
    font-size: clamp(2rem, 3.6vw, 3.35rem);
  }

  .auth-shell__brand-company {
    margin-top: 0.35rem;
  }

  .auth-shell__headline {
    margin-top: clamp(1rem, 2.2vh, 1.6rem);
  }

  .auth-shell__headline p {
    font-size: clamp(1rem, 1.35vw, 1.32rem);
  }

  .auth-shell__headline h1 {
    margin-top: 0.7rem;
    font-size: clamp(2.15rem, 3.1vw, 3.55rem);
    line-height: 1.02;
  }

  .auth-shell__illustration {
    align-self: center;
    width: min(100%, 42rem);
    max-height: 42vh;
    margin: clamp(0.65rem, 1.6vh, 1.35rem) auto;
    min-height: 0;
  }

  .auth-shell__svg {
    max-height: min(42vh, 24rem);
    object-fit: contain;
  }

  .auth-shell__floating-card {
    min-width: 7.25rem;
    padding: 0.65rem 0.85rem;
    font-size: 0.82rem;
  }

  .auth-shell__floating-card--1 {
    left: 4%;
    top: 30%;
  }

  .auth-shell__floating-card--2 {
    right: 5%;
    top: 24%;
  }

  .auth-shell__floating-card--3 {
    left: 7%;
    bottom: 16%;
  }

  .auth-shell__floating-card--4 {
    right: 7%;
    bottom: 20%;
  }

  .auth-shell__benefits {
    gap: 0.65rem;
    margin-top: 0;
  }

  .auth-shell__benefit {
    border-radius: 0.85rem;
    padding: 0.72rem;
  }

  .auth-shell__benefit strong {
    font-size: 0.86rem;
  }

  .auth-shell__benefit span {
    margin-top: 0.25rem;
    font-size: 0.74rem;
    line-height: 1.32;
  }

  .auth-shell__right {
    height: 100%;
    min-height: 0;
    align-items: stretch;
    overflow-x: hidden;
    overflow-y: auto;
    padding: clamp(1rem, 3vw, 3rem);
  }

  .auth-shell__card {
    margin-block: auto;
  }
}

@media (max-width: 420px) {
  .auth-shell__right {
    padding-inline: 0.75rem;
  }

  .auth-shell__card {
    padding: 1.15rem;
  }
}

@media (prefers-reduced-motion: reduce) {
  .auth-shell__building,
  .auth-shell__tower,
  .auth-shell__student,
  .auth-shell__floating-card {
    animation: none;
  }
}
</style>
