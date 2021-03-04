function addTagFormDeleteLink($tagFormLi) {
    var removeFormButton = document.createElement('a')

    removeFormButton.innerHTML = 'Supprimer'
    removeFormButton.setAttribute('href', '#')
    removeFormButton.className = 'remove-button'

    $tagFormLi.append(removeFormButton)
    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault()
        // remove the row for the tag form
        $tagFormLi.remove()
    })
}  

document.addEventListener("DOMContentLoaded", () => {
    console.log('Entrypoint widget-collection: DOMContentLoaded')
    let counter = 0

    // Get the section that holds the collection
    // var wrapper = document.getElementById('templateskills-fields-list')
    var wrappers = document.querySelectorAll('[id$=-fields-list]')
    
    wrappers.forEach(wrapper => {
        wrapper.querySelectorAll('div.add-remove-button').forEach(item => addTagFormDeleteLink(item))
    })

    //Catch the link used to trigger the event
    var triggers = document.getElementsByClassName('add-another-collection-widget')

    if (typeof(triggers) != 'undefined' && triggers != null) {
        triggers.forEach(element => {
            element.addEventListener('click', (e) => {
                e.preventDefault()

                // get #templateskills-fields-list
                var list = document.querySelector(element.getAttribute('data-list-selector')) 
    
                // Try to find the counter of the list or use the length of the list
                counter = list.dataset.widgetCounter || list.children.length
                
                // grab the prototype template
                let newWidget = list.getAttribute('data-prototype')
                // replace the "__name__" used in the id and name of the prototype
                // with a number that's unique to your emails
                // end name attribute looks like name="contact[emails][2]"
                newWidget = newWidget.replace(/__name__/g, counter)
                // Increase the counter
                counter++
                // And store it, the length cannot be used if deleting widgets is allowed
                list.dataset.widgetCounter = counter
                
                // create a new list element and add it to the list
                var newElem = document.createElement(list.getAttribute('data-widget-tagname'))
                newElem.className = "collection-wrapper"
                newElem.innerHTML = newWidget.trim()
    
                var rowElem = newElem.childNodes[0] 
                rowElem.className += " " + document.getElementsByClassName('collection-row')[0].getAttribute('class')
                // rowElem.className += " add-remove-button"
                rowElem.childNodes.forEach( element => element.className += " " + document.getElementsByClassName('collection-col')[0].getAttribute('class'))            
            
                // add a delete link to the new form
                addTagFormDeleteLink(rowElem)
    
                // add element to list
                list.appendChild(newElem)
            })
        })

    }  
})