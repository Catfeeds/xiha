<?php
$config = array (
		'seller_id'=>"2088021589743498",
		//应用ID,您的APPID。
		'app_id' => "2017072707915333",

		//商户私钥
		'merchant_private_key' => 'MIIEogIBAAKCAQEAuGmAbtI5hFnVwGLkgZVuxeYxXtSRm5xqlqsomLWe+AqYOwLoyDNuV8CkPofo/BZPs2d0auJsVn9srN1HmCA/339ociYGEYZ/kp5u0DY91XIn9KWU9JKYDi2oblliCnlGZ/dlSoJ3EhiByNhQaldiyXSgjtWs0eXPyMTwtaGXYJWn5F608MUpg7+xSf76Qv6cuHS7bgi/F7Dyggceg26up7xr9qLARw0rj3jHb/EIhGDZwZtPCmXmgOAo0LU/dux2kQuSTVggpZRYHUn69ciCulPGRQnNzn+nfKVXUXpS8K8GNDkZvwUCpHRp8/SHUs8w6R0MhcUAllH9E48FaoOX5QIDAQABAoIBAGJL8iagLgJrNDGxROYBtiMMmWJX4ilVDce0AhasMMk8NHq4CUa1i2qBB8tA/KJqbh9N1NMoT+EnWNEnvgLLpeBG9HBr7i4nSBbqFl0rnHgKuluAa06WWTCti6sOP+EfVbUTPM2jjz55C6z4CJ02aTTC0tZv9QeI/OGigEAAm0TieJI7esf1WUbQ2ObB2Zj7PaiM6V3NFuwdK611Z+NRUMmcuKh2o9blZ06v3pObwWm1V4zQyfeZDH4iGtlTZlLrRGGw3GUMMFoKcnSKw5yKVybYvH09HqFr9ylkzqQBus9gO/tSxNkG65rLwcpC/b1ZQ8OLPrh0IIatfkQ6198k/t0CgYEA6AvtlfZDrwsjacmf9jRsmG5cd3/Suyt4PaQ9VMDFbwcxodoypabzx47aCCG5we194noWwLrGSh2UYK9U8bwL7S3dWeLTHokLB7KwyG1ZO7ABDtyNZUxksevH4CNAfxnXt2Zq2+DByNk4aVDMrLwQc/lUTFSUyfyUMoQb9YuHjfcCgYEAy3LIe1OlfO6nb2CxUyrUXNTnTjT4SJwKr6xdj6B82lQPZLNVQhd9hBIJyF/eDajfP6NTDgLq+2BLW1phnjMqw1FsGZvIlkftxjvZeRDUYZUFBOme/dNz2tCdRO6CxH4yiu+c673FQxaYmyl6WyO/xx/ZvGBVHmsl1sz4SF13AgMCgYAilPyAd+YYZTmO33yWbbOtd+0R5hvaxxyxWxPE2MHTzSepbmJT67CzEegOhDAx5Zn4MafIa0136DafviGTsSm53Db72WELUPMy33+XLyy4R3+w3k+4RA1Rssjj730CNDDA5WORh0p6C11r3J3UNGbjUcotYgGWpKzVZnjgPD4kewKBgAyXVgEL9Dn/Ky2hdlgHVo1IP+h/Lr6PqbmlmUTCUffRLAK3dpdYjNQXqbNU+5Es2mQ9324GkDRuiTtmJJR95i+gYmhTU965JOYX3iW3/OztJBphuw1KgOkR8CnL+RvgE5C1s6iDXioAxAEWWBQ76iqQthEKhToUt4rLtLIF1s+xAoGAZLT/YO+dN/Xol8bwwDcMcvmLQ7GeKAvwzYssKXaanJLmfjaWdlXQmtiodLjB0Ar1O3gMorwoL53umtoVZiIDzLw+82oqm6HcKDeN+K6LBtHIHkJo80spTZUG9zJf7o/I/sId1tRj2ar5JV6Q2vshvG8bSqGIEfUpIxJUglcUwn0=',

		//异步通知地址
		'notify_url' => "http://www.miaomimouse.com/index.php?con=trade&act=alipay_notify_url",

		//同步跳转
		'return_url' => "http://www.miaomimouse.com/index.php?con=trade&act=alipay_return_url",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsM3MY5ZQjaa0maH8oiFJ7EeeOpDkvfREc5cQFzS1AlGa9FbRHx8TsCzngBKUsbNYze++4GfouVqXhBjYJZEcsS28KI45zKVzTNRYIjSXCvrS7Dcfjc4I8Ummft5wn79/L7zC4MV+eSizGWVdUr5pM+zyuoL0dyNVNpKpE56CdCnk2GIiG+I6Ev5Z5KiCbyVTl+jIda7BR+pjnGBNwvErixZLLcUWIFyxSg9PWDTbxvdG5jGL+IcXk9/AHFvpTUSeeKZqDAIYW+5ph8Drvdk9YFeKKk6k+ITOm47nvvc+HTp7+U+2hw8c22tgDYf2myy4I//3Cv7aU87FSM3G/8ochQIDAQAB',
);