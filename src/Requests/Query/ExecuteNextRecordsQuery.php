<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Query;

class ExecuteNextRecordsQuery extends QueryRequest
{
    public function __construct(protected string $nextRecordsUrl) {}

    public function resolveEndpoint(): string
    {
        return $this->nextRecordsUrl;
    }

    public function defaultQuery(): array
    {
        return [];
    }
}
