window.onload = function() {
    $('#espinhaco').click(function() {
        download('http://cncflora.jbrj.gov.br/arquivos/arquivos/pdfs/PAN_Espinhaco Meridional.pdf',
                 'http://cncflora.jbrj.gov.br/arquivos/arquivos/pdfs/Anexos_PAN_Espinhaco Meridional.pdf');
    });

    $('#espinhaco2').click(function() {
        download('http://cncflora.jbrj.gov.br/arquivos/arquivos/pdfs/PAN_Espinhaco Meridional.pdf',
                 'http://cncflora.jbrj.gov.br/arquivos/arquivos/pdfs/Anexos_PAN_Espinhaco Meridional.pdf');
    });

    $('#graomogol').click(function() {
        download('http://cncflora.jbrj.gov.br/arquivos/arquivos/pdfs/PAN_Grao Mogol.pdf',
                 'http://cncflora.jbrj.gov.br/arquivos/arquivos/pdfs/Anexos_Grao Mogol.pdf');
    });

    $('#graomogol2').click(function() {
        download('http://cncflora.jbrj.gov.br/arquivos/arquivos/pdfs/PAN_Grao Mogol.pdf',
                 'http://cncflora.jbrj.gov.br/arquivos/arquivos/pdfs/Anexos_Grao Mogol.pdf');
    });
    var download = function() {
        for(var i=0; i<arguments.length; i++) {
            var iframe = $('<iframe style="visibility: collapse;"></iframe>');
            $('body').append(iframe);
            var content = iframe[0].contentDocument;
            var form = '<form action="' + arguments[i] + '" method="GET"></form>';
            content.write(form);
            $('form', content).submit();
            setTimeout((function(iframe) {
                return function() {
                    iframe.remove();
                }
            })(iframe), 2000);
        }
    }
}
