import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['slide'];

    current = 0;
    dragStartX = null;

    connect() {
        this.render();
    }

    dragStart(event) {
        this.dragStartX = event.clientX;
    }

    dragEnd(event) {
        if (this.dragStartX === null) return;

        const delta = event.clientX - this.dragStartX;
        this.dragStartX = null;

        const threshold = 40;
        if (delta > threshold) this.prev();
        else if (delta < -threshold) this.next();
    }

    prev() {
        this.current = (this.current - 1 + this.slideTargets.length) % this.slideTargets.length;
        this.render();
    }

    next() {
        this.current = (this.current + 1) % this.slideTargets.length;
        this.render();
    }

    goTo(event) {
        this.current = event.params.index;
        this.render();
    }

    render() {
        const total = this.slideTargets.length;

        this.slideTargets.forEach((slide, index) => {
            let offset = index - this.current;
            if (offset > total / 2) offset -= total;
            if (offset < -total / 2) offset += total;

            slide.style.setProperty('--offset', offset);
            slide.style.setProperty('--abs-offset', Math.min(Math.abs(offset), 3));
            slide.dataset.active = offset === 0 ? 'true' : 'false';
        });
    }
}