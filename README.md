TangoMan Repository Helper
==========================

**TangoMan Repository Helper** provides trait with useful functions for your repositories.

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require tangoman/repository-helper
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.


Usage
=====

Inside your repository
----------------------

Add "use" statement just like when you're using a trait.

```php
<?php

namespace FoobarBundle\Repository;

use Doctrine\ORM\EntityRepository;
use TangoMan\RepositoryHelper\RepositoryHelper;

/**
 * Class FoobarRepository
 */
class FoobarRepository extends EntityRepository
{
    use RepositoryHelper;
}
```

Inside your controller
----------------------

```php
use Symfony\Component\HttpFoundation\Request;

class FoobarController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated user list
        $em = $this->get('doctrine')->getManager();
        $foobars = $em->getRepository('AppBundle:Foobar')->findByQuery($request);

        return $this->render(
            'admin/foobar/index.html.twig',
            [
                'foobars' => $foobars,
            ]
        );
    }
```

Inside your views
-----------------

### Search Form
```twig
<label for="inputUser">User</label>
<input type="text" name="user-username" id="inputUser" class="form-control"
    value="{{ app.request.get('user-username')|join(', ') }}"/>
```
Will generate this:
.../admin/posts/?user-username=admin


### Order Link
```twig
<th class="{{ app.request.query.get('order') == 'user-username' ? app.request.query.get('way', 'ASC') }}">
    <a href="{{ path('app_admin_post_index', app.request.query.all|merge({
        'page'  : 1,
        'order' : 'user-username',
        'way'   : app.request.query.get('order') == 'user-username'
        and app.request.query.get('way', 'ASC') == 'ASC' ? 'DESC' : 'ASC'})) }}">
        User
    </a>
</th>
```
Will generate this:
.../admin/posts/?page=1&order=user-username&way=ASC

Helper Public Methods
=====================

|                       function                       |                                       description                                       |        parameters       |       return      |
|------------------------------------------------------|-----------------------------------------------------------------------------------------|-------------------------|-------------------|
| getTableName()                                       | Returns current table name                                                              | n/a                     | string            |
| countByCriteria($criteria = [])                                | Returns element count, filtered by given criteria                                       | array                   | integer           |
| distinct($property, $criteria = [])                  | Lists distinct items from desired column, filtered by given criteria                    | string, array           | array             |
| findAllPaged($page = 1, $limit = 10, $criteria = []) | Returns X results with pagination, starting from given page, filtered by given criteria | integer, integer, array | (object)Paginator |
| findByQuery(Request $request, $criteria = [])        | Returns query result with pagination, filtered by given criteria                        | (object)Request, array  | (object)Paginator |
| findByQueryScalar(Request $request, $criteria = [])  | Return query as scalar result with pagination, filtered by given criteria               | (object)Request, array  | (object)Paginator |
| export(Request $request, $criteria = [])             | Return all objects as scalar result, filtered by given criteria                         | (object)Request, array  | array             |

Query Parameters
================

| parameter |   type  |         value          |
|-----------|---------|------------------------|
| order     | string  | switch-entity-property |
| way       | string  | `(ASC/DESC)`           |
| limit     | integer | `[1-9]+\d?`            |
| page      | integer | `[1-9]+\d?`            |
| join      | string  | switch-entity-property |

Switches
========

Switch values for mode/operator/action

| switch |   mode   | description |
|--------|----------|-------------|
| a      | andWhere | andWhere    |
| b      | search   | boolean     |
| c      | order    | count       |
| e      | search   | exactMatch  |
| l      | search   | like        |
| n      | search   | notNull     |
| o      | orWhere  | orWhere     |
| p      | order    | property    |
| r      | order    | orderBy     |
| s      | search   | simpleArray |

Error
=====

When symfony raises this exception:

> Semantical Error line 0, col 55 near 'company LIKE': Error: Invalid PathExpression. Must be a StateFieldPathExpression.

![semantical error][semantical-error]

It means that you have an error inside your form: `<input name="foo-bar">`, attribute doesn't target appropriate joined entity.

Try `<input name="foo-bar-title">`, or `<input name="bar-title">`, **TangoMan Repository Helper** will automatically handle the join.

Note
====

If you find any bug please report here : [Issues](https://github.com/TangoMan75/RepositoryHelper/issues/new)

License
=======

Copyrights (c) 2018 Matthias Morin

[![License][license-MIT]][license-url]
Distributed under the MIT license.

If you like **TangoMan Repository Helper** please star!
And follow me on GitHub: [TangoMan75](https://github.com/TangoMan75)
... And check my other cool projects.

[Matthias Morin | LinkedIn](https://www.linkedin.com/in/morinmatthias)

[license-GPL]: https://img.shields.io/badge/Licence-GPLv3.0-green.svg
[license-MIT]: https://img.shields.io/badge/Licence-MIT-green.svg
[license-url]: LICENSE
[semantical-error]: Resources/doc/semantical_error.jpg
