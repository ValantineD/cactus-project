import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'

document.addEventListener('DOMContentLoaded', function () {

    const btnExact = document.getElementById('btnExact')
    const btnFlexible = document.getElementById('btnFlexible')
    const pickerEl = document.getElementById('inline-calendar')
    const hiddenInput = document.getElementById('selected-date')

    if (!btnExact || !btnFlexible || !pickerEl) return

    let pickerCalendar = null
    let isOpen = false


    function setActive(activeBtn) {
        btnExact.classList.remove('date-toggle-btn--active')
        btnFlexible.classList.remove('date-toggle-btn--active')
        activeBtn.classList.add('date-toggle-btn--active')
    }


    function openPicker() {
        pickerEl.style.display = 'block'
        isOpen = true

        setActive(btnExact)

        if (pickerCalendar) return

        pickerCalendar = new Calendar(pickerEl, {
            plugins: [dayGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            height: 'auto',
            firstDay: 1,
            locale: 'fr',

            dateClick: function (info) {
                const date = info.dateStr

                hiddenInput.value = date

                // show selected date in pill
                btnExact.textContent = date

                pickerEl.style.display = 'none'
                isOpen = false

                setActive(btnExact)
            }
        })

        pickerCalendar.render()
    }


    btnExact.addEventListener('click', (e) => {
        e.stopPropagation()
        openPicker()
    })

    btnFlexible.addEventListener('click', () => {

        hiddenInput.value = ''

        // reset button text
        btnExact.textContent = 'Choisir une date'

        setActive(btnFlexible)

        pickerEl.style.display = 'none'
        isOpen = false
    })

    document.addEventListener('click', (e) => {

        if (!isOpen) return

        const clickedInside =
            pickerEl.contains(e.target) ||
            e.target === btnExact

        if (!clickedInside) {
            pickerEl.style.display = 'none'
            isOpen = false
        }
    })


    setActive(btnFlexible)

    hiddenInput.value = ''
    pickerEl.style.display = 'none'
})
