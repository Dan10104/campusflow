import { registerSW } from 'virtual:pwa-register';

const createUpdatePrompt = (reloadCallback) => {
    if (document.querySelector('[data-campusflow-pwa-update]')) {
        return;
    }

    const container = document.createElement('div');
    container.dataset.campusflowPwaUpdate = 'true';
    container.setAttribute('role', 'status');
    container.style.cssText = [
        'position:fixed',
        'left:1rem',
        'right:1rem',
        'bottom:1rem',
        'z-index:9999',
        'display:flex',
        'align-items:center',
        'justify-content:space-between',
        'gap:1rem',
        'max-width:720px',
        'margin-inline:auto',
        'border:1px solid #BFDBFE',
        'border-radius:16px',
        'background:#FFFFFF',
        'box-shadow:0 22px 60px rgba(15,23,42,.18)',
        'padding:1rem',
        'color:#0F172A',
        'font-family:inherit',
    ].join(';');

    const message = document.createElement('p');
    message.textContent = 'Hay una nueva versión de CampusFlow disponible.';
    message.style.cssText = 'margin:0;font-size:.92rem;font-weight:800;line-height:1.45;color:#334155';

    const actions = document.createElement('div');
    actions.style.cssText = 'display:flex;align-items:center;gap:.6rem;flex:none';

    const laterButton = document.createElement('button');
    laterButton.type = 'button';
    laterButton.textContent = 'Después';
    laterButton.style.cssText = 'border:1px solid #E2E8F0;border-radius:12px;background:#fff;color:#334155;padding:.65rem .85rem;font-size:.82rem;font-weight:900;cursor:pointer';
    laterButton.addEventListener('click', () => container.remove());

    const reloadButton = document.createElement('button');
    reloadButton.type = 'button';
    reloadButton.textContent = 'Actualizar';
    reloadButton.style.cssText = 'border:1px solid #2563EB;border-radius:12px;background:#2563EB;color:#fff;padding:.65rem .9rem;font-size:.82rem;font-weight:900;cursor:pointer';
    reloadButton.addEventListener('click', () => {
        reloadButton.disabled = true;
        reloadCallback();
    });

    actions.append(laterButton, reloadButton);
    container.append(message, actions);
    document.body.appendChild(container);
};

export const registerCampusFlowPwa = () => {
    if (!('serviceWorker' in navigator)) {
        return;
    }

    let updateServiceWorker;

    updateServiceWorker = registerSW({
        immediate: true,
        onNeedRefresh() {
            createUpdatePrompt(() => updateServiceWorker?.(true));
        },
        onOfflineReady() {
            window.dispatchEvent(new CustomEvent('campusflow:pwa-offline-ready'));
        },
        onRegisterError(error) {
            console.warn('No se pudo registrar el Service Worker de CampusFlow.', error);
        },
    });
};
