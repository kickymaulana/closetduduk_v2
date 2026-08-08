<?php

use App\Mcp\Servers\AppFlowServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('appflow', AppFlowServer::class);

// Mcp::web('/mcp/demo', \App\Mcp\Servers\PublicServer::class);
