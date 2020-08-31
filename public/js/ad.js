$('#add-image').click(function(){
    //on recuper le numero du bloc a creer
    const index = +$('#widgets-counter').val();

    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g,index);

    //on injecte ce code au sein de la dive
    $('#ad_images').append(tmpl);

   $('#widgets-counter').val(index + 1);

    //on suprimme le formulaire concerné
    handleDeleteButton();
});

function handleDeleteButton(){
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        
        $(target).remove();

    });
}

function updateCounter(){
    const count = +$('#ad_images div.form-group').length;
    $('#widgets-counter').val(count);
}

updateCounter();

handleDeleteButton();