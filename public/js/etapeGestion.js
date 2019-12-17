$('#add-etape').click(function () {
    const index = +$('#widgets-counter').val();
    const tmpl = $('#recette_etape').data('prototype').replace(/__name__/g, index);
    console.log(tmpl);
    $('#recette_etape').append(tmpl);
    $('#widgets-counter').val(index +1);
    handleDeleteButtons();
});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounter() {
    const count = +$('#recette_etape div.form-group').length;

    $('#widgets-counter').val(count);
}

updateCounter();
handleDeleteButtons();