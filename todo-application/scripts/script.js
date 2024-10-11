const nav = document.querySelector('.header'); // selecting the header section of a page

//fetching the contents of header.html
fetch('header.html').then(response => {
    return response.text();
}).then(data => {
    nav.innerHTML = data; //inserting the contents of header.html into the header section of the page
})