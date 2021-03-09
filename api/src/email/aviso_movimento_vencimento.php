<?php
    require_once(__DIR__ . '/../rest/autoload.php');

    header('Content-Type: text/html; charset=utf-8');
    ob_start();

?>

<a href="https://www.adelsonguimaraes.com.br/meufinanceiro/" style="text-decoration:none;">
    <div style="border: 1px solid black; min-height: 250px; width: 600px; padding:10px; background: #7faf4b;">
        <div style="padding:15px; width:100%; color: black; text-align: center; font-size: 35px;font-family: sans-serif;">AVISO DE VENCIMENTO</div>
        <div style="display: flex; padding: 10px;">
            <img height="120" width="20%" style="margin-top: 30px;" src="https://www.adelsonguimaraes.com.br/meufinanceiro/libs/img/icons/icon-512x512.png" alt="">
            <div style="flex:1; padding: 10px; color:black;font-size: 18px; font-family: sans-serif; text-align: justify;">
                Olá <b><?php echo $data['nome'];?></b>, alguns de seus movimentos vencem daqui há <?php echo $dias_para_vencer ?> dias (<?php echo formatDate($data['vencimento']) ?>).
                <br><br>
                Segue abaixo a lista:
                <br>
                <ul>
                <?php foreach($data['movimentos'] as $key):?>
                
                <li><?php echo $key['nome'] ." - " . formatMoeda($key['valor']) ?></li>
                
                <?php endforeach;?>
                </ul>
                <br>
                
                <small>Vá para o sistema clicando nessa mensagem.</small>
            </div>
        </div>
    </div>
</a>