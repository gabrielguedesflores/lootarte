<?php
date_default_timezone_set('America/Sao_Paulo');
$date = date("d-m-Y-Hi");
$path = getcwd();

#PERCORRE A PASTA IMAGENS E GUARDA O NOME DAS PASTAS EM ARRAY
$baseDir = 'imagens/';
$openDir = dir($baseDir);

$dirArray = array();

while ($arq = $openDir->read()):

    if($arq != '.' && $arq != '..'):
        $dirArray[] = $arq;
    endif;    

endwhile;    

#INSERE O CABEÇALHO DO ARQUIVO
$cabecalho = array(
    'Fulfillment Id' => 'Fulfillment Id',
    'Mock Front' => 'Mock Front',
    'File Front' => 'File Front',
    'Mock Back' => 'Mock Back',
    'File Back' => 'File Back',
    'First Name' => 'First Name',
    'Last Name' => 'Last Name',
    'Street' => 'Street',
    'Number' => 'Number',
    'Complement' => 'Complement',
    'Neighborhood' => 'Neighborhood',
    'City' => 'City',
    'State' => 'State',
    'Zipcode' => 'Zipcode',
    'Phone' => 'Phone',
    'Dimona SKU' => 'Dimona SKU',
    'Qty' => 'Qty',
    'Shipping Speed' => 'Shipping Speed',
    'Double-Sided' => 'Double-Sided',
    'CPF/CNPJ' => 'CPF/CNPJ',
);

$file = "/pedidos/orders_" . $date . ".xlsx";
$fp = fopen($path . $file, "w");
fputcsv($fp , $cabecalho, ";", chr(127)); 


include 'Controlador.php';
$instanciaClasse = new Request();

##informar o último caracter no request
$Json = $instanciaClasse->executeRequest("pedido/search/?limit=50&situacao_id=4&");  //<-- usar p/ teste
//$Json = $instanciaClasse->executeRequest("pedido/search/?since_numero=1&situacao_id=4&"); 

    foreach ($Json->objects as $api) {
    
        $getFilterOrder = $instanciaClasse->executeRequest("pedido/$api->numero/?");

        foreach ($getFilterOrder->itens as $item) {
            foreach ($dirArray as $value) {
                
                foreach ($getFilterOrder->envios as $frete) {

                    $pos = strpos($item->sku, $value);

                    if($pos !== FALSE){
                    //if(str_contains($item->sku, $value)){

                        #FATIA A STRING DO NOME --- SPIA-CM-10110118309
                        $partes = explode(' ', $getFilterOrder->cliente->nome);
                        $firstName = array_shift($partes);
                        $lastName= array_pop($partes);

                        #FATIA A STRING DO SKU
                        $fatiaSku = explode('-', $item->sku);
                        $estampa = array_shift($fatiaSku);
                        $skuDimona= array_pop($fatiaSku);

                        if ($fatiaSku[0] == 'MZ'){

                            $row = array(

                                'Fulfillment Id' => $getFilterOrder->numero,
                                'Mock Front' => null,
                                'File Front' => null,
                                'Mock Back' => 'https://sistemalootarte.fun/imagens/' . $value . '/' . $fatiaSku[0] . '-MOCKUP.png',
                                'File Back' => 'https://sistemalootarte.fun/imagens/' . $value . '/' . $value . '.png',
                                'First Name' => $firstName,
                                'Last Name' => $lastName,
                                'Street' => $getFilterOrder->endereco_entrega->endereco,
                                'Number' => $getFilterOrder->endereco_entrega->numero,
                                'Complement' => $getFilterOrder->endereco_entrega->complemento,
                                'Neighborhood' => $getFilterOrder->endereco_entrega->bairro,
                                'City' => $getFilterOrder->endereco_entrega->cidade,
                                'State' => $getFilterOrder->endereco_entrega->estado,
                                'Zipcode' => $getFilterOrder->endereco_entrega->cep,
                                'Phone' => $getFilterOrder->cliente->telefone_celular,
                                //'Dimona SKU' => $item->sku,
                                'Dimona SKU' => $skuDimona,
                                'Qty' => intval($item->quantidade),
                                'Shipping Speed' => $frete->forma_envio->nome,
                                'Double-Sided' => 'FALSE',
                                'CPF/CNPJ' => $getFilterOrder->endereco_entrega->cnpj . '/' . $getFilterOrder->endereco_entrega->cpf,
                            );

                            fputcsv($fp, $row, ";", chr(127));

                            ###altera status do pedido para em produção
                            $instanciaClasse->alterOrder($getFilterOrder->numero);

                        }else{

                            $row = array(

                                'Fulfillment Id' => $getFilterOrder->numero,
                                'Mock Front' => 'https://sistemalootarte.fun/imagens/' . $value . '/' . $fatiaSku[0] . '-MOCKUP.png',
                                'File Front' => 'https://sistemalootarte.fun/imagens/' . $value . '/' . $value . '.png',
                                'Mock Back' => null,
                                'File Back' => null,
                                'First Name' => $firstName,
                                'Last Name' => $lastName,
                                'Street' => $getFilterOrder->endereco_entrega->endereco,
                                'Number' => $getFilterOrder->endereco_entrega->numero,
                                'Complement' => $getFilterOrder->endereco_entrega->complemento,
                                'Neighborhood' => $getFilterOrder->endereco_entrega->bairro,
                                'City' => $getFilterOrder->endereco_entrega->cidade,
                                'State' => $getFilterOrder->endereco_entrega->estado,
                                'Zipcode' => $getFilterOrder->endereco_entrega->cep,
                                'Phone' => $getFilterOrder->cliente->telefone_celular,
                                //'Dimona SKU' => $item->sku,
                                'Dimona SKU' => $skuDimona,
                                'Qty' => intval($item->quantidade),
                                'Shipping Speed' => $frete->forma_envio->nome,
                                'Double-Sided' => 'FALSE',
                                'CPF/CNPJ' => $getFilterOrder->endereco_entrega->cnpj . '/' . $getFilterOrder->endereco_entrega->cpf,
                            );

                            fputcsv($fp, $row, ";", chr(127));

                            ###altera status do pedido para em produção
                            $instanciaClasse->alterOrder($getFilterOrder->numero);
                        }    

                    }else{
                        echo '<h3>Skus antigos</h3>';
                    }
                }    
            }    
        }
    }    







