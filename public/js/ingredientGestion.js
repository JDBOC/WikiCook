$('#add-image').click(function () {
    const index = +$('#widget-counter').val();
    const tmpl = $('#recette_ingredient').data('prototype').replace(/__name__/g, index);
    console.log(tmpl);
    $('#recette_ingredient').append(tmpl);
    $('#widget-counter').val(index +1);
    handleDeleteButtons();
});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounter() {
    const count = +$('#recette_ingredient div.form-group').length;

    $('#widgets-counter').val(count);
}

updateCounter();
handleDeleteButtons();