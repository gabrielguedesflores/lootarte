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

$dadosXls = ""; 
$dadosXls .= " <meta charset='utf-8'>"; 
$dadosXls .= " <table>"; 
$dadosXls .= " <tr>"; 

$dadosXls .= " <th>Fulfillment Id</th>"; 
$dadosXls .= " <th>Mock Front</th>"; 
$dadosXls .= " <th>File Front</th>"; 
$dadosXls .= " <th>Mock Back</th>"; 
$dadosXls .= " <th>File Back</th>"; 
$dadosXls .= " <th>First Name</th>"; 
$dadosXls .= " <th>Last Name</th>"; 
$dadosXls .= " <th>Street</th>"; 
$dadosXls .= " <th>Number</th>"; 
$dadosXls .= " <th>Complement</th>"; 
$dadosXls .= " <th>Neighborhood</th>"; 
$dadosXls .= " <th>City</th>"; 
$dadosXls .= " <th>State</th>"; 
$dadosXls .= " <th>Zipcode</th>"; 
$dadosXls .= " <th>Phone</th>"; 
$dadosXls .= " <th>Dimona SKU</th>"; 
$dadosXls .= " <th>Qty</th>"; 
$dadosXls .= " <th>Shipping Speed</th>"; 
$dadosXls .= " <th>Double-Sided</th>"; 
$dadosXls .= " <th>CPF/CNPJ</th>"; 
$dadosXls .= " </tr>"; 

## FIM 

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
                    // $iden = substr($getFilterOrder->endereco_entrega->cpf, 0, 1);
                    // echo $iden;
                    
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

                        // if(substr($getFilterOrder->endereco_entrega->cpf, 0, 1) == "0"){
                        //     $iden = '0' . $getFilterOrder->endereco_entrega->cpf;
                        // }
                        // else{
                        //     $iden = $getFilterOrder->endereco_entrega->cpf;
                        // }
                        // $iden = strval($getFilterOrder->endereco_entrega->cpf);
                        

                        if ($fatiaSku[0] == 'MZ'){


                            $dadosXls .= " <tr>"; 
                                $dadosXls .= " <td>$getFilterOrder->numero,</td>"; 
                                $dadosXls .= " <td></td>"; 
                                $dadosXls .= " <td></td>"; 
                                $dadosXls .= " <td>https://sistemalootarte.fun/imagens/" . $value . "/" . $fatiaSku[0] . "-MOCKUP.png</td>"; 
                                $dadosXls .= " <td>https://sistemalootarte.fun/imagens/" . $value . "/" . $value . ".png</td>"; 
                                $dadosXls .= " <td>" . $firstName . "</td>"; 
                                $dadosXls .= " <td>" . $lastName . "</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->endereco . "</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->numero ."</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->complemento . "</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->bairro . "</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->cidade . "</td>";
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->estado . "</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->cep . "</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->cliente->telefone_celular . "</td>"; 
                                $dadosXls .= " <td>" . $skuDimona . "</td>"; 
                                $dadosXls .= " <td>" . intval($item->quantidade) . "</td>"; 
                                $dadosXls .= " <td>" . $frete->forma_envio->nome . "</td>"; 
                                $dadosXls .= " <td>FALSE</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->cpf . "</td>"; 
                            $dadosXls .= " </tr>"; 


                            ###altera status do pedido para em produção
                            $instanciaClasse->alterOrder($getFilterOrder->numero);

                        }else{


                            $dadosXls .= " <tr>"; 
                                $dadosXls .= " <td>$getFilterOrder->numero,</td>"; 
                                $dadosXls .= " <td>https://sistemalootarte.fun/imagens/" . $value . "/" . $fatiaSku[0] . "-MOCKUP.png</td>"; 
                                $dadosXls .= " <td>https://sistemalootarte.fun/imagens/" . $value . "/" . $value . ".png</td>"; 
                                $dadosXls .= " <td></td>"; 
                                $dadosXls .= ' <td></td>'; 
                                $dadosXls .= " <td>" . $firstName . "</td>"; 
                                $dadosXls .= " <td>" . $lastName . "</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->endereco . "</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->numero ."</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->complemento . "</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->bairro . "</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->cidade . "</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->estado . "</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->cep . "</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->cliente->telefone_celular . "</td>"; 
                                $dadosXls .= " <td>" . $skuDimona . "</td>"; 
                                $dadosXls .= " <td>" . intval($item->quantidade) . "</td>"; 
                                $dadosXls .= " <td>" . $frete->forma_envio->nome . "</td>"; 
                                $dadosXls .= " <td>FALSE</td>"; 
                                $dadosXls .= " <td>" . $getFilterOrder->endereco_entrega->cpf . "</td>"; 
                            $dadosXls .= " </tr>"; 

                            ###altera status do pedido para em produção
                            $instanciaClasse->alterOrder($getFilterOrder->numero);
                        }    

                    }else{
                        //echo '<h3>Skus antigos</h3>';
                    }
                }    
            }    
        }
    }    


$dadosXls .= " </table>"; 

// Definimos o nome do arquivo que será exportado 
$arquivo = "orders_" . $date . ".xlsx";

// Configurações header para forçar o download 
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/vnd.ms-excel'); 
header('Content-Disposition: attachment;filename="'.$arquivo.'"'); 
header('Cache-Control: max-age=0'); 
//header("Content-Type: application/json; charset=utf-8");

// Se for o IE9, isso talvez seja necessário 
header('Cache-Control: max-age=1'); 

// Envia o conteúdo do arquivo 

echo $dadosXls; 
exit; 





