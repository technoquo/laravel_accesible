<?php

test('the application redirects root to the default locale', function () {
    $this->get('/')->assertRedirect('/es');
});
