import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import momentPlugin from '@fullcalendar/moment';
import { event } from 'jquery';

console.log('Entrypoint calendar-widget: Ready')

// let minDate = new Date('today'), maxDate = new Date('today +1 month')
let minTime = '7:00', maxTime = '21:00'

// Layout
let loader = document.getElementById('loader-wrapper')
let modal = document.getElementById('modal-container')
let modalContent = document.getElementById('modal-body')
let calendarEl = document.getElementById('calendar-container')

var eventsUrl = calendarEl.dataset.eventsUrl;

// vérifier la taille de l'écran
function mobileCheck() {
    if (window.innerWidth <= 768 ) {
        return true
    }
    return false
}

let calendar = new Calendar(calendarEl, {
    locale: 'fr',
    timeZone: 'UTC',
    plugins: [interactionPlugin, dayGridPlugin, timeGridPlugin, listPlugin, momentPlugin],
    expandRows: true,
    height: '100%',
    stickyHeaderDates: true,
    handleWindowResize: true,
    initialView: mobileCheck() ? 'listMonth' : 'dayGridMonth',
    selectable: false,
    navLinks: false,
    customButtons: {
        customTitle: {
            text: false
        },
        choseView: {
            text: false
        },
        toggleSidebar: {
            text: false
        }
    },
    buttonText: {
        today: false,
        month: 'mois',
        week: 'semaine',
        day: 'jour',
        list: 'liste'
    },
    headerToolbar: {
        start: 'prev,customTitle,next today',
        center: 'choseView dayGridMonth,dayGridWeek,listMonth',
        end: 'toggleSidebar',
    },
    slotMinTime: minTime,
    slotMaxTime: maxTime,
    allDaySlot: false,
    weekNumbers: true,
    weekNumberFormat: { week: "numeric" },
    firstDay: 1,
    nowIndicator: true,
    eventSources: [
        {
            url: eventsUrl,
            method: "POST",
            extraParams: {
                filters: JSON.stringify({})
            },
            failure: () => {
                alert("Une erreur s'est produite lors de la récupération des événements !");
            },
        },
    ],
    noEventsContent: 'Aucun événement à afficher',
    progressiveEventRendering: true,
    eventClick: (info => {
        // Empêcher la redirection
        info.jsEvent.preventDefault()

        let url = info.event.url
        let allEvents = calendar.getEvents()    
        
        // chercher les événements sélectionnés
        allEvents.forEach(ev => {
            if (ev.extendedProps.isSelected == true) {
                // éditer isSelected pour supprimer la classe
                ev.setExtendedProp('isSelected', false)
            }
        })
        // attribuer la classe à l'élément cliqué
        info.event.setExtendedProp('isSelected', true)
        // et afficher l'indicateur de chargement...
        loader.style.display = 'flex'

        fetch(url, {
            method: "GET"
        })
        .then(response => response.text())
        .then(html => {            
            // remplacer le corps de la modale
            // et afficher la modale
            modalContent.innerHTML = html
            if (!modal.classList.contains('show')) modal.classList.add('show')
            // puis masquer la page de chargement
            loader.style.display = 'none'
        })
        .then(() => {
            let trigger = document.getElementById('registration-btn')

            // si 'trigger' n'est pas trouvé, c'est qu'il est masqué pour cet utilisateur
            if (typeof (trigger) != 'undefined' && trigger != null) {
                trigger.addEventListener('click', (ev) => {
                    let url = trigger.getAttribute('href')
                    ev.preventDefault()
                    loader.style.display = 'flex'

                    fetch(url, {
                        method: "GET"
                    })
                    .then(response => response.text())
                    .then(html => {
                        modalContent.innerHTML = html
                        loader.style.display = 'none'
                    })
                })
            }
        })
    }),
    eventClassNames: (arg) => {
        if (arg.event.extendedProps.isSelected) {
            return ['event-selected']
        } else {
            return ['']
        }
    },
    windowResize: () => {
        return setView()
    }
})

function setView() {
    // Définir la vue par défaut
    let view = calendar.view.type
    let viewChoiceDropdownEl = document.getElementById('viewChoiceDropdown') // fc-chunk qui contient le btn-group + le déclencheur
    let todayButton = document.getElementsByClassName('fc-today-button')[0]
    todayButton.innerHTML = 'aujourd\'hui'

    // afficher par défaut tous les boutons du dropdown,
    // et masquer le bouton du qui déclenche le dropdown
    if (viewChoiceDropdownEl.classList.contains('mobile')) viewChoiceDropdownEl.classList.remove('mobile')
    choseViewButton.classList.remove('show')

    if (mobileCheck()) {
        // définir la vue par défaut à 'liste'
        // et changer l'icône du bouton du dropdown
        view = "listMonth"
        choseViewButton.setAttribute('data-current', view)
        todayButton.innerHTML = 'auj.'
        
        // réduire le dropdown à un seul bouton
        if (!viewChoiceDropdownEl.classList.contains('mobile')) viewChoiceDropdownEl.classList.add('mobile')
        choseViewButton.classList.add('show')
    }   
    // changer la vue
    calendar.changeView(view)
}

document.addEventListener('DOMContentLoaded', () => {
    calendar.render()
    
    // récupérer les élements du calendrier
    // attribuer des ids pour les atteindre plus facilement
    let customTitleEl = document.getElementsByClassName('fc-customTitle-button')[0]
    let fcButtons = document.getElementsByClassName('fc-button')
    let choseViewButton = document.getElementsByClassName('fc-choseView-button')[0]
    choseViewButton.parentNode.setAttribute('id', 'viewChoiceDropdown')
    let btnGroup = choseViewButton.nextElementSibling
    btnGroup.setAttribute('id', 'choseViewMenu')
    let toggleSidebar = document.getElementsByClassName('fc-toggleSidebar-button')[0]
    let closeModalButton = document.getElementById('closeModalButton')

    // initialiser le contenu
    customTitleEl.innerHTML = calendar.view.title
    customTitleEl.setAttribute('disabled', 'disabled')
    choseViewButton.setAttribute('id', 'choseViewButton')
    choseViewButton.setAttribute('data-toggle', 'dropdown')
    choseViewButton.setAttribute('aria-haspopup', 'true')
    choseViewButton.setAttribute('aria-expanded', 'true')  

    toggleSidebar.setAttribute('id', 'toggleSidebar')
    toggleSidebar.setAttribute('data-toggle', 'modal')
    toggleSidebar.setAttribute('data-target', '#modal-container')
    
    // afficher les infos à l'ouverture
    if (!modal.classList.contains('show')) modal.classList.add('show')
    setView()

    // rafraichir à chaque clic sur un bouton de la toolbar
    fcButtons.forEach(btn => { 
        btn.addEventListener('click', () => {
            choseViewButton.setAttribute('data-current', calendar.view.type)
            customTitleEl.innerHTML = calendar.view.title
        })
    })
    
    // toggle modal
    toggleSidebar.addEventListener('click', () => {
        modal.classList.toggle('show')
    })   
    // close modal
    closeModalButton.addEventListener('click', () => {
        modal.classList.remove('show')
    })
})

document.addEventListener('click', (evt) => {
    // élément clické
    let targetElement = evt.target
    let choseViewMenu = document.getElementById('choseViewMenu')

    do {
        if (targetElement == choseViewButton) {
            // ... déclencher le comportement attendu du bouton
            choseViewMenu.classList.toggle('show')
            return
        }
        // sinon... élément du DOM
        targetElement = targetElement.parentNode;
    } while (targetElement)

    // clic en dehors
    if (choseViewMenu.classList.contains('show')) {
        choseViewMenu.classList.remove('show')
    }
})

document.addEventListener('keyup', (e) => {
    if (e.code === 'Escape') modal.classList.remove('show')
})