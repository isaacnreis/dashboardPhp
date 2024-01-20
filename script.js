$(document).ready(() => {

  $('#documentacao').on('click', () => {
    $('#pagina').load('documentacao.html');
  });

  $('#suporte').on('click', () => {
    $('#pagina').load('suporte.html');
  });

  $('#competencia').on('change', e => {
    let competencia = $(e.target).val();
    $.ajax({
      type: 'GET',
      url: 'app.php',
      dataType: 'json',
      data: `competencia=${competencia}`,
      success: dados => {
        console.log(dados);
        $('#numeroVendas').html(dados.numeroVendas);
        $('#totalVendas').html(dados.totalVendas);
        $('#clientesAtivos').html(dados.clientesAtivos);
        $('#clientesInativos').html(dados.clientesInativos);
        $('#totalReclamacoes').html(dados.totalReclamacoes);
        $('#totalElogios').html(dados.totalElogios);
        $('#totalSugestoes').html(dados.totalSugestoes);
        $('#totalDespesas').html(dados.totalDespesas);
      },
      error: error => {console.log(error)}
    })
  });
})