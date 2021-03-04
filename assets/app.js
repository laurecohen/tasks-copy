/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';


// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

document.addEventListener('DomContentLoaded', () => {
    console.log('Entrypoint app: Ready')
})
    
// Grab hamburger menu element
// then grab target element
let navbarToggler = document.getElementById('nav-toggler')

if (typeof (navbarToggler) != 'undefined' && navbarToggler != null) {
    let targetEl = document.getElementById(navbarToggler.getAttribute('aria-controls'))
    
    navbarToggler.addEventListener('click', (e) => {
        // prevent the navigator to navigate
        e.preventDefault()
        // then toggle the display of the menu
        targetEl.classList.toggle('show')
    })    
    
    window.addEventListener('resize', () => {
        // if window is resized close the menu
        if (targetEl.classList.contains('show')) targetEl.classList.remove('show')
    })
}

let listItemTriggers = document.getElementsByClassName('dropdown-trigger')

if (typeof (listItemTriggers) != 'undefined' && listItemTriggers != null) {
    listItemTriggers.forEach(item => {

        item.addEventListener('click', (e) => {
            e.preventDefault()
            
            // Récupérer l'élément liste
            item.nextElementSibling.classList.toggle('show')
            item.parentNode.classList.toggle('expanded')
        })
    })
}