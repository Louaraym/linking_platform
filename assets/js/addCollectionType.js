/*<div id="advert_categories"
       data-prototype="<fieldset class=&quot;form-group&quot;>
                        <legend class=&quot;col-form-label required&quot;>__name__label__</legend>
                        <div id=&quot;advert_categories___name__&quot;>
                            <div class=&quot;form-group&quot;>
                                <label for=&quot;advert_categories___name___name&quot; class=&quot;required&quot;>Nom</label>
                                <input type=&quot;text&quot;
                                       id=&quot;advert_categories___name___name&quot;
                                       name=&quot;advert[categories][__name__][name]&quot;
                                       required=&quot;required&quot;
                                       placeholder=&quot;Saisir le nom de la catégorie&quot;
                                       class=&quot;form-control&quot;
                                   />
                            </div>
                        </div>
                    </fieldset>">
</div>*/

$(function (){

    // On récupère la balise <div> en question qui contient l'attribut « data-prototype » qui nous intéresse.
    let $container = $('div#advert_categories');

    // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
    let index = $container.find(':input').length;

    // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
    $('#add_category').click(function(event) {
        event.preventDefault(); // évite qu'un # apparaisse dans l'URL

        addCategory($container);

        return false;
    });

    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un (cas d'une nouvelle annonce par exemple).
    if (index === 0) {
        addCategory($container);
    } else {
        // S'il existe déjà des catégories, on ajoute un lien de suppression pour chacune d'entre elles
        $container.children('div').each(function() {
            addDeleteLink($(this));
        });
    }

    // La fonction qui ajoute un formulaire CategoryType
    function addCategory($container) {
        /* Dans le contenu de l'attribut « data-prototype », on remplace :
         - le texte "__name__label__" qu'il contient par le label du champ
         - le texte "__name__" qu'il contient par le numéro du champ */
        let template = $container.attr('data-prototype')
            .replace(/__name__label__/g, 'Catégorie n°' + (index+1))
            .replace(/__name__/g,        index)
        ;

        // On crée un objet jquery qui contient ce template
        let $prototype = $(template);

        // On ajoute au prototype un lien pour pouvoir supprimer la catégorie
        addDeleteLink($prototype);

        // On ajoute le prototype modifié à la fin de la balise <div>
        $container.append($prototype);

        // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
        index++;
    }

    // La fonction qui ajoute un lien de suppression d'une catégorie
    function addDeleteLink($prototype) {
        // Création du lien
        let $deleteLink = $('<a href="#" class="btn btn-danger"> <i class="fas fa-trash-alt"></i> Supprimer la catégorie</a>');

        // Ajout du lien
        $prototype.append($deleteLink);

        // Ajout du listener sur le clic du lien pour effectivement supprimer la catégorie
        $deleteLink.click( function(event) {
            $prototype.remove();

            event.preventDefault(); // évite qu'un # apparaisse dans l'URL

            return false;
        });
    }

})