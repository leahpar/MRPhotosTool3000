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

**plus nécessaire**

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

**plus nécessaire**

```php
# ./vendor/easycorp/easyadmin-bundle/src/Event/BeforeEntityDeletedEvent.php
# line 10
final class BeforeEntityDeletedEvent
{
    use StoppableEventTrait; // <== add trait

    // ...
}
```

### InvalidArgumentException: "A coordinate cannot be positioned outside of a bounding box (x: 0, y: -0.5 given)"

```php
# ./vendor/imagine/imagine/src/Image/Point.php
public function __construct($x, $y)
{
    $this->x = ($x < 0 ? 0 : $x);
    $this->y = ($y < 0 ? 0 : $y);

    /*
    if ($x < 0 || $y < 0) {
        throw new InvalidArgumentException(sprintf('A coordinate cannot be positioned outside of a bounding box (x: %s, y: %s given)', $x, $y));
    }

    $this->x = $x;
    $this->y = $y;
    */
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

### Facebook

Obtention d'un token longue durée :

https://developers.facebook.com/docs/facebook-login/access-tokens/refreshing/

curl -i -X GET "https://graph.facebook.com/v16.0/oauth/access_token\
?grant_type=fb_exchange_token\
&client_id=xxxx\
&client_secret=xxxx\
&fb_exchange_token=xxxx"

curl -i -X GET "https://graph.facebook.com/v16.0/oauth/access_token\
?grant_type=fb_exchange_token\
&client_id=403558550207195\
&client_secret=9b7311a14a504befa30a29a43bb209bd\
&fb_exchange_token=EAAFvCMwHZBtsBO37kdzGTtBh693UMHNrjVnoLO551xbYUd9OG3GRIEFGUdCEmiXSKt3O87ZAAEOniVWV1niZAGlynG8cESypJxM9jZChRDTVfFhgKVZAwZBjuXaoZCQz52P6kR8Dm7TeaCzsyU7nqKm66PYOpcJonQAa8uRXAmZCZCPPypTjjVyXmIjXUueKuvAJyM6pgkeDkRZA0zzkw1zbm02i7X"

https://graph.facebook.com/v16.0/17841406271014748?fields=id%2Cname%2Cfollowers_count&access_token=EAAFvCMwHZBtsBAEwE5C6A3oeO7jPI0zHSBDOHu0fKEdVnyyNaH0D7BFc0kKsUXZB6ZAgpVQ0T9GsVo9uasECMdR1oIL2znp12PdgZC0utmfrkM6LEwXd0nqEW1QfbtsYZBy7G0FJvXEz6sWZBHOxH1R5XLLBAVurQxiwiTNYRPNAZDZD
https://graph.facebook.com/v16.0/?fields=id%2Cname%2Cfollowers_count&access_token=EAAFvCMwHZBtsBAEwE5C6A3oeO7jPI0zHSBDOHu0fKEdVnyyNaH0D7BFc0kKsUXZB6ZAgpVQ0T9GsVo9uasECMdR1oIL2znp12PdgZC0utmfrkM6LEwXd0nqEW1QfbtsYZBy7G0FJvXEz6sWZBHOxH1R5XLLBAVurQxiwiTNYRPNAZDZD

`fb_exchange_token` = 'token utilisteur'
sur https://developers.facebook.com/tools/explorer/

`client_id` et `client_secret` = 'identifiant d'application' et 'clé secrète' 
sur https://developers.facebook.com/apps/<APP>/settings/basic/
https://developers.facebook.com/apps/403558550207195/settings/basic/

# Prolonger la durée de validité du token

https://developers.facebook.com/tools/debug/accesstoken/?access_token=xxxx


# rsync

rsync -avz -e "ssh" --progress raphael@elune.ovh:/home/raphael/mr-photographes.fr/var/shootings .

# certbot

certbot certonly --webroot \
    -w /home/raphael/mr-photographes.fr/public \
    -d mr-photographes.fr \
    -d www.mr-photographes.fr \
    -d mrphotographes.fr \
    -d www.mrphotographes.fr \
    -d mrphotographes.com \
    -d www.mrphotographes.com \
    -d mr-photographes.com \
    -d www.mr-photographes.com


## TODO

### MAJ date dernier shooting

```sql
UPDATE modele m SET date_dernier_shooting = (
    SELECT MAX(date) FROM shooting s left join shooting_modele sm on s.id = sm.shooting_id WHERE sm.modele_id = m.id
);

```

### DESIGN

- [X] /login
- [X] page d'accueil des shootings
- [X] Shooting > lien "retour" 
- [X] Accès admin sur la page d'accueil
- [ ] page d'erreur
- [ ] admin.css : ".index-" => ".ea-index-"
- [ ] admin.css : ".content-panel" => ".content"
- [ ] admin.css : ".ea-index-Photo .content .table.datagrid tbody tr td.field-image" => bg-color KO (mode sombre)

### DEV

- [x] ajout emoji étoile
- [x] Photos > recherche par nom de modèle
- [x] Photos > nombre par pages %4
- [x] Photos > tri date de shooting DESC
- [x] Front > Affichage mois des shootings en fr
- [X] Admin Shooting > lien voir les photos (photos filtre shooting)
- [X] Admin Galeries > lien voir les photos (photos filtre shooting)
- [X] Front > Lien dl zip
- [ ] accessvoter pour la gestion des photos / shootings / galeries / public / privé / couv...


curl 'https://mr-photographes.fr/shootings/2019-06-26-brise-dete/001-IMG_7597-Modifier.jpg?filter=front' \
-H 'Connection: keep-alive' \
-H 'Sec-Fetch-Site: same-origin' \
-H 'Sec-Fetch-Mode: same-origin' \
-H 'Sec-Fetch-Dest: empty' \
-H 'Referer: https://mr-photographes.fr/shootings/2019-06-26-brise-dete' \
-H 'Cookie: PHPSESSID=stlueh442qpna688ld7evponrh' \
-H 'If-Modified-Since: Sun, 28 Feb 2021 10:13:59 GMT' \
--compressed




