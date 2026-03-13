<?php

// главная страница
test('page home returns a redirect response', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get('/');
    $response->assertRedirect(route('login'));
});
test('page home returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser();

    $response = $this->actingAs($user)->get('/');
    $response->assertOk();
});
// страница шаблонов
test('page template-list returns a redirect response', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('table-settings.template-list'));
    $response->assertRedirect(route('login'));
});
test('page template-list returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser();

    $response = $this->actingAs($user)->get(route('table-settings.template-list'));
    $response->assertOk();
});
// страница продуктов шаблона
test('page template-list-product returns a redirect response', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('table-settings.product-list', ['template_id' => 1]));
    $response->assertRedirect(route('login'));
});
test('page template-list-product returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser();

    $response = $this->actingAs($user)->get(route('table-settings.product-list', ['template_id' => 1]));
    $response->assertOk();
});
// страница групп узлов
test('page configuration-node-group returns a redirect response', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('configuration-node-group'));
    $response->assertRedirect(route('login'));
});
test('page configuration-node-group returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser();

    $response = $this->actingAs($user)->get(route('configuration-node-group'));
    $response->assertOk();
});
// страница ткп контактов
test('page tkp-contact returns a redirect response', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('tkp.contact'));
    $response->assertRedirect(route('login'));
});
test('page tkp-contact returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser();

    $response = $this->actingAs($user)->get(route('tkp.contact'));
    $response->assertOk();
});
// страница редактирования контакта ткп
test('page tkp-contact-edit returns a redirect response', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('tkp.contact.edit', ['id' => 1, 'tkp_version' => 1]));

    $response->assertRedirect(route('login'));
});
test('page tkp-contact-edit returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser();
    $tkp = createTkp($user);
    
    $response = $this->actingAs($user)->get(route('tkp.contact.edit', ['id' => $tkp->id, 'tkp_version' => $tkp->tkp_version]));
    $response->assertOk();
});
// страница редактирования схемы ткп
test('page tkp-sheme-edit returns a redirect response', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('tkp.sheme.edit', ['id' => 1, 'tkp_version' => 1]));

    $response->assertRedirect(route('login'));
});
test('page tkp-sheme-edit returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser();
    $tkp = createTkp($user);
    
    $response = $this->actingAs($user)->get(route('tkp.sheme.edit', ['id' => $tkp->id, 'tkp_version' => $tkp->tkp_version]));
    $response->assertOk();
});
// страница редактирования доставки ткп
test('page tkp-delivery-edit returns a redirect response', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('tkp.delivery.edit', ['id' => 1, 'tkp_version' => 1]));

    $response->assertRedirect(route('login'));
});
test('page tkp-delivery-edit returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser();
    $tkp = createTkp($user);
    
    $response = $this->actingAs($user)->get(route('tkp.delivery.edit', ['id' => $tkp->id, 'tkp_version' => $tkp->tkp_version]));
    $response->assertOk();
});
// страница редактирования итоговой страницы ткп
test('page tkp-calculation-edit returns a redirect response', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('tkp.calculation.edit', ['id' => 1, 'tkp_version' => 1]));

    $response->assertRedirect(route('login'));
});
test('page tkp-calculation-edit returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser();
    $tkp = createTkp($user);
    
    $response = $this->actingAs($user)->get(route('tkp.calculation.edit', ['id' => $tkp->id, 'tkp_version' => $tkp->tkp_version]));
    $response->assertOk();
});
// страница отображения pdf ткп
test('page tkp-pdf-show returns a redirect response', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('tkp.pdf.show', ['id' => 1, 'tkp_version' => 1]));

    $response->assertRedirect(route('login'));
});
test('page tkp-pdf-show returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser();
    $tkp = createTkp($user);
    createConfiguration($tkp);
    
    $response = $this->actingAs($user)->get(route('tkp.pdf.show', ['id' => $tkp->id, 'tkp_version' => $tkp->tkp_version]));
    $response->assertOk();
});
// страница экспорта
test('page excel-import returns a redirect response', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('table-settings.products.excel-import'));

    $response->assertRedirect(route('login'));
});
test('page excel-import returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser();

    $response = $this->actingAs($user)->get(route('table-settings.products.excel-import'));
    $response->assertOk();
});
// страница схемы измерений
test('page dimension-schemes returns a redirect response', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('table-settings.dimension-schemes', ['template_id' => 1]));
    $response->assertRedirect(route('login'));
});
test('page dimension-schemes returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser();

    $response = $this->actingAs($user)->get(route('table-settings.dimension-schemes', ['template_id' => 1]));
    $response->assertOk();
});
// страница списка инженерных изысканий
test('page engineering-list returns a redirect response', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('tkp.engineering-list'));
    $response->assertRedirect(route('login'));
});
test('page engineering-list returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser();

    $response = $this->actingAs($user)->get(route('tkp.engineering-list'));
    $response->assertOk();
});
// страница списка производителей
test('page manufacturer-list returns a redirect response', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('tkp.manufacturer-list'));
    $response->assertRedirect(route('login'));
});
test('page manufacturer-list returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser();

    $response = $this->actingAs($user)->get(route('tkp.manufacturer-list'));
    $response->assertOk();
});
