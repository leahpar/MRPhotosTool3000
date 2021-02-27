# MR Photo Tool 3000

## Install

### Droits d'écriture pour les miniatures

```bash
cd public
mkdir media
chmod 777 media
```

## Patch dans les vendors

### Ajout annotation 'date' dans les types sluggables

```php
# ./vendor/gedmo/doctrine-extensions/src/Sluggable/Mapping/Driver/Annotation.php
# line 42
protected $validTypes = [
    'string',
    'text',
    'integer',
    'int',
    'date', // <== add 'date'
    'datetime',
    'datetimetz',
    'citext',
];
```

### Attempted to call an undefined method named "isPropagationStopped"

```php
# ./vendor/easycorp/easyadmin-bundle/src/Event/BeforeEntityDeletedEvent.php
# line 10
final class BeforeEntityDeletedEvent
{
    use StoppableEventTrait; // <== add trait

    // ...
}
```

### InvalidArgumentException - The controller for URI "" is not callable

```twig
{# ./vendor/easycorp/easyadmin-bundle/src/Resources/views/crud/form_theme.html.twig #}
{# line 483 #}
{% for action in batch_actions %}
    <button name="{{ form.crudAction.vars.full_name }}"
            value="{{ action.crudActionName }}"
            class="ask-confirm-batch-button {{ action.cssClass }}"
            {% for name, value in action.htmlAttributes %}{{ name }}="{{ value|e('html_attr') }}" {% endfor %}>
        <span class="btn-label">
            {%- if action.icon %}<i class="action-icon {{ action.icon }}"></i> {% endif -%}
            {%- if action.label is not empty -%}{{ action.label }}{%- endif -%}
        </span>
    </button>
{% endfor %}
```

### Fork EasyAdmin avec les batch actions

- https://medium.com/swlh/using-your-own-forks-with-composer-699358db05d9
- https://stackoverflow.com/questions/6022302/how-to-apply-unmerged-upstream-pull-requests-from-other-forks-into-my-fork

## TODO

### DESIGN

- [X] /login
- [X] page d'accueil des shootings
- [ ] Shooting > lien "retour" 
- [ ] page d'erreur

### DEV

- [x] ajout emoji étoile
- [x] Photos > recherche par nom de modèle
- [x] Photos > nombre par pages %4
- [x] Photos > tri date de shooting DESC
- [x] Front > Affichage mois des shootings en fr
- [X] Admin Shooting > lien voir les photos (photos filtre shooting)
- [X] Admin Galeries > lien voir les photos (photos filtre shooting)
- [ ] Front > Lien dl zip
- [ ] accessvoter pour la gestion des photos / shootings / galeries / public / privé / couv...

