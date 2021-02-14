# MR Photo Tool 3000

## Patch dans les vendors

### Ajout annotation 'date' dans les types sluggables

```php
# ./gedmo/doctrine-extensions/src/Sluggable/Mapping/Driver/Annotation.php line 42
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

### Fork EasyAdmin avec les batch actions

- https://medium.com/swlh/using-your-own-forks-with-composer-699358db05d9
- https://stackoverflow.com/questions/6022302/how-to-apply-unmerged-upstream-pull-requests-from-other-forks-into-my-fork

## TODO

### DESIGN

- /login
- page d'erreur générique

### DEV

- accessvoter pour la gestion des photos / shootings / galeries / public / privé / couv...

