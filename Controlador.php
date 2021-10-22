<?php 

class Request
{
    public function executeRequest($endpoint)
    {
        $chaveApi = "{apiKey}";
        $chaveAplicação = "{apiKey}";
        $url = "https://api.awsli.com.br/v1/$endpoint"."format=json&chave_api=$chaveApi&chave_aplicacao=$chaveAplicação";
        $retornoJson = file_get_contents($url);
        $retornoJsonDecode = json_decode($retornoJson);
        return $retornoJsonDecode;
    }

    public function alterOrder($pedido_id)
    {
        $chaveApi = "{apiKey}";
        $chaveAplicação = "{apiKey}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.awsli.com.br/v1/situacao/pedido/$pedido_id/?format=json&chave_api=$chaveApi&chave_aplicacao=$chaveAplicação");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"codigo\": \"em_producao\"}");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json"
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    

    public function returnSizeSku($sku, $size)
    {
        $var = $sku;
        $tamanhoVar = mb_strlen($var);      //pega a qtd de caracteres da string 
        $ultimos8 = substr($var, -$size);   //quebra a string 
        $tamanhoVar8 = mb_strlen($ultimos8);//pega a qtd de caracteres depois da quebra 
        return $ultimos8;
    }
    
}


// $instancia = new Request(); 

// echo $instancia->alterOrder(31);
