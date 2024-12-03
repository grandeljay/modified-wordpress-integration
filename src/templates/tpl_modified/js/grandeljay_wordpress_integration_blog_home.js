"use strict";

document.addEventListener('DOMContentLoaded', function() {
    initialise();
});

function initialise() {
    initialiseReadMore();
}

function initialiseReadMore() {
    let aReadMoreLink    = document.getElementById('read_more_link');
    let aReadLessLink    = document.getElementById('read_less_link');
    let aReadMoreContent = document.getElementById('read_more_content');

    aReadMoreLink.classList.remove('hide');
    aReadMoreLink.classList.add('show');

    aReadMoreLink.addEventListener('click', function(event) {
        event.preventDefault();

        aReadMoreLink.classList.remove('show');
        aReadMoreLink.classList.add('hide');

        aReadLessLink.classList.remove('hide');
        aReadLessLink.classList.add('show');

        aReadMoreContent.classList.remove('hide');
        aReadMoreContent.classList.add('show');
    });

    aReadLessLink.addEventListener('click', function(event) {
        event.preventDefault();

        aReadMoreLink.classList.remove('hide');
        aReadMoreLink.classList.add('show');

        aReadLessLink.classList.remove('show');
        aReadLessLink.classList.add('hide');

        aReadMoreContent.classList.remove('show');
        aReadMoreContent.classList.add('hide');
    });
}
