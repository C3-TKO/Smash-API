<?php

namespace AppBundle\Pagination;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class PaginationFactory
{
    const PAGE_DEFAULT_COUNT = 20;
    const MAX_PAGE_COUNT = 100;

    const PARAMETER_NAME_PAGE_NUMBER = 'pageNumber';
    const PARAMETER_NAME_PAGE_SIZE = 'pageSize';

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function createCollection(QueryBuilder $qb, Request $request, $route, array $routeParams = array())
    {
        $page = $request->query->get(self::PARAMETER_NAME_PAGE_NUMBER, 1);
        $count = $request->query->get(self::PARAMETER_NAME_PAGE_SIZE, self::PAGE_DEFAULT_COUNT);
        if ( $count > self::MAX_PAGE_COUNT ) {
            $count = self::MAX_PAGE_COUNT;
        }
        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($count);
        $pagerfanta->setCurrentPage($page);
        $programmers = [];

        foreach ($pagerfanta->getCurrentPageResults() as $result) {
            $programmers[] = $result;
        }
        $paginatedCollection = new PaginatedCollection($programmers, $pagerfanta->getNbResults());

        // make sure query parameters are included in pagination links
        $routeParams = array_merge($routeParams, $request->query->all());

        $createLinkUrl = function($targetPage) use ($route, $routeParams) {
            return $this->router->generate($route, array_merge(
                $routeParams,
                array(self::PARAMETER_NAME_PAGE_NUMBER => $targetPage)
            ));
        };
        $paginatedCollection->addLink('self', $createLinkUrl($page));
        $paginatedCollection->addLink('first', $createLinkUrl(1));
        $paginatedCollection->addLink('last', $createLinkUrl($pagerfanta->getNbPages()));

        if ($pagerfanta->hasNextPage()) {
            $paginatedCollection->addLink('next', $createLinkUrl($pagerfanta->getNextPage()));
        }

        if ($pagerfanta->hasPreviousPage()) {
            $paginatedCollection->addLink('prev', $createLinkUrl($pagerfanta->getPreviousPage()));
        }

        return $paginatedCollection;
    }
}