    const scroller = document.getElementById('moment-scroll');
    const prevBtn  = document.getElementById('moment-prev');
    const nextBtn  = document.getElementById('moment-next');
    const STEP = 300;

    if (prevBtn && nextBtn && scroller) {
        prevBtn.addEventListener('click', () => scroller.scrollBy({ left: -STEP, behavior: 'smooth' }));
        nextBtn.addEventListener('click', () => scroller.scrollBy({ left:  STEP, behavior: 'smooth' }));
    }
