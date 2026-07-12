import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['nav', 'button'];

    toggle() {
        const isOpen = this.navTarget.classList.toggle('flex');
        this.navTarget.classList.toggle('hidden', !isOpen);
        this.buttonTarget.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    close() {
        this.navTarget.classList.remove('flex');
        this.navTarget.classList.add('hidden');
        this.buttonTarget.setAttribute('aria-expanded', 'false');
    }
}