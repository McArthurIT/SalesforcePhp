<?php

namespace myoutdeskllc\SalesforcePhp\Connectors;

use Illuminate\Support\Str;
use myoutdeskllc\SalesforcePhp\Requests\Query\ExecuteNextRecordsQuery;
use myoutdeskllc\SalesforcePhp\SalesforceApi;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\PaginationPlugin\Paginator;

class SalesforceApiConnector extends Connector implements HasPagination
{
    public function resolveBaseUrl(): string
    {
        return SalesforceApi::getInstanceUrl().'/services/data/'.SalesforceApi::getApiVersion();
    }

    public function paginate(Request $request): Paginator
    {
        return new class(connector: $this, request: $request) extends Paginator
        {
            protected function isLastPage(Response $response): bool
            {
                return $response->json('done') === true;
            }

            protected function getPageItems(Response $response, Request $request): array
            {
                return $response->json('records');
            }

            protected function applyPagination(Request $request): Request
            {
                if ($this->currentResponse instanceof Response) {
                    return new ExecuteNextRecordsQuery(
                        str_replace(
                            '/services/data/'.SalesforceApi::getApiVersion(),
                            '',
                            $this->currentResponse->json('nextRecordsUrl')
                        )
                    );
                }

                return $request;
            }
        };
    }}
