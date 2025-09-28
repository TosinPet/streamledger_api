<?php

it('has streamledger page', function () {
    $response = $this->get('/streamledger');

    $response->assertStatus(200);
});
