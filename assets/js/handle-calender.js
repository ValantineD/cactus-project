import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import * as bootstrap from 'bootstrap'

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar')
    if (!calendarEl) return

    const modalEl = document.getElementById('event-modal')
    const modal = new bootstrap.Modal(modalEl)

    const desktopToolbar = {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,listMonth'
    }

    const mobileToolbar = {
        left: 'dayGridMonth,timeGridWeek,listMonth',
        center: 'title',
        right: 'prev,next today'
    }

    const getToolbar = () => {
        return window.innerWidth < 768 ? mobileToolbar : desktopToolbar
    }

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        initialView: 'dayGridMonth',

        headerToolbar: getToolbar(),

        locale: 'fr',
        nowIndicator: true,
        height: 'auto',
        firstDay: 1,

        buttonText: {
            today: 'Aujourd’hui',
            month: 'Mois',
            week: 'Semaine',
            list: 'Liste'
        },

        events: {
            url: calendarEl.dataset.eventsUrl,
            method: 'GET',
            failure: () => alert('Failed to load events.')
        },

        eventContent: function (arg) {
            const icon = arg.event.extendedProps.icon
            const title = arg.event.title
            const time = arg.timeText

            let html = ''

            if (time) {
                html += `<span class="fc-event-time">${time}&nbsp;</span>`
            }

            if (icon) {
                html += `<img src="${icon}"
                    alt=""
                    style="width:14px;height:14px;object-fit:contain;margin-right:4px;vertical-align:middle;">`
            }

            html += `<span class="fc-event-title">${title}</span>`

            return { html }
        },

        eventClick: function (info) {
            const p = info.event.extendedProps
            const color = info.event.backgroundColor

            const modalTitle = document.getElementById('modal-title')
            const modalHeader = document.getElementById('modal-header')
            const modalBody = document.getElementById('modal-body')
            const modalLink = document.getElementById('modal-link')

            if (modalHeader) {
                modalHeader.style.setProperty('--event-color', color)
            }

            modalTitle.textContent = info.event.title

            const start = info.event.start?.toLocaleString('fr-FR') ?? '—'
            const end = info.event.end?.toLocaleString('fr-FR') ?? '—'

            let rows = `
                <li><strong>Début:</strong> ${start}</li>
                <li><strong>Fin:</strong> ${end}</li>
                <li><strong>Lieu:</strong> ${p.location ?? '—'}</li>
                <li><strong>Description:</strong> ${p.description ?? '—'}</li>
            `

            if (p.type === 'created') {
                rows += `
                    <li><strong>Places restantes:</strong> ${p.remaining} / ${p.spots}</li>
                `
            } else {
                rows += `
                    <li><strong>Créateur:</strong> ${p.organizer ?? '—'}</li>
                    <li><strong>Ma participation:</strong> ${p.participationStatus ?? '—'}</li>
                `
            }

            modalBody.innerHTML = rows

            const rawId = String(info.event.id).replace('p_', '')
            modalLink.href = `/activity/${rawId}`

            if (p.icon) {
                modalTitle.innerHTML = `
                    <img src="${p.icon}"
                         style="width:20px;height:20px;object-fit:contain;margin-right:6px;vertical-align:middle;">
                    ${info.event.title}
                `
            }

            modal.show()
        }
    })

    calendar.render()

    // Responsive toolbar update
    window.addEventListener('resize', () => {
        calendar.setOption('headerToolbar', getToolbar())
    })
})
