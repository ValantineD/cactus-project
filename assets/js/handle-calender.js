// assets/calendar.js

// assets/js/handle-calender.js

import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import * as bootstrap from 'bootstrap' // 👈 add this


document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar')
    if (!calendarEl) return  // only run on pages that have the calendar

    const modal = new bootstrap.Modal(document.getElementById('event-modal'))

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        locale: 'fr',
        nowIndicator: true,
        height: 'auto',

        events: {
            url: calendarEl.dataset.eventsUrl,  // passed from Twig, see below
            method: 'GET',
            failure: () => alert('Failed to load events.')
        },

        eventClick: function (info) {
            const p = info.event.extendedProps
            const color = info.event.backgroundColor

            document.getElementById('modal-title').textContent = info.event.title
            document.getElementById('modal-header').style.setProperty('--event-color', color)

            const start = info.event.start?.toLocaleString('fr-FR') ?? '—'
            const end   = info.event.end?.toLocaleString('fr-FR')   ?? '—'

            let rows = `
                <li><strong>📅 Start:</strong> ${start}</li>
                <li><strong>🏁 End:</strong> ${end}</li>
                <li><strong>📍 Location:</strong> ${p.location ?? '—'}</li>
                <li><strong>📝 Description:</strong> ${p.description ?? '—'}</li>
            `

            if (p.type === 'created') {
                rows += `
                    <li><strong>🪑 Spots:</strong> ${p.remaining} / ${p.spots} remaining</li>
                    <li><strong>📌 Status:</strong> ${p.status ?? '—'}</li>
                    <li><strong>🔓 State:</strong> ${p.state ?? '—'}</li>
                `
            } else {
                rows += `
                    <li><strong>👤 Organizer:</strong> ${p.organizer ?? '—'}</li>
                    <li><strong>✅ My status:</strong> ${p.participationStatus ?? '—'}</li>
                `
            }

            document.getElementById('modal-body').innerHTML = rows

            const rawId = info.event.id.replace('p_', '')
            document.getElementById('modal-link').href = `/activity/${rawId}`

            modal.show()
        }
    })

    calendar.render()
})
