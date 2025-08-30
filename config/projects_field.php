<?php
return [
    'tienda_online' => [
        ['name' => 'productos_a_vender', 'label' => '¿Productos a vender?', 'type' => 'boolean'],
        ['name' => 'login_usuarios', 'label' => '¿Login y gestión de usuarios?', 'type' => 'boolean'],
        ['name' => 'carrito', 'label' => '¿Carrito de compras?', 'type' => 'boolean'],
        ['name' => 'cms', 'label' => 'CMS preferido', 'type' => 'text'],
        ['name' => 'metodos_pago', 'label' => 'Métodos de pago', 'type' => 'select', 'options' => ['Tarjeta de crédito', 'PayPal', 'Transferencia bancaria', 'Otros']],
        ['name' => 'envio', 'label' => 'Métodos de envío', 'type' => 'select', 'options' => ['Nacional', 'Internacional']],
        //mas información
        ['name' => 'informacion_adicional', 'label' => 'Información adicional', 'type' => 'textarea'],
    ],
    'web_corporativa' => [
        ['name' => 'tipo_web', 'label' => 'Tipo de web', 'type' => 'select', 'options' => ['Landing', 'Blog', 'Multi-página']],
        ['name' => 'cms', 'label' => '¿Usar CMS?', 'type' => 'boolean'],
        ['name' => 'num_paginas', 'label' => 'Número de páginas', 'type' => 'number'],
        ['name' => 'formulario_contacto', 'label' => '¿Formulario de contacto?', 'type' => 'boolean'],
        ['name' => 'seo', 'label' => 'Optimización SEO', 'type' => 'boolean'],
        //información adicional
        ['name' => 'informacion_adicional', 'label' => 'Información adicional', 'type' => 'textarea'],
    ],
    'portafoliol' => [
        ['name' => 'tipo_portafolio', 'label' => 'Tipo de portafolio', 'type' => 'select', 'options' => ['Personal', 'Profesional']],
        ['name' => 'proyectos_destacados', 'label' => 'Proyectos destacados', 'type' => 'textarea'],
        ['name' => 'habilidades', 'label' => 'Habilidades', 'type' => 'text'],
        ['name' => 'experiencia_laboral', 'label' => 'Experiencia laboral', 'type' => 'textarea'],
        ['name' => 'informacion_adicional', 'label' => 'Información adicional', 'type' => 'textarea'],
    ]
];
