# Tchurminha - Racha Conta

App web para dividir a conta entre amigos. Adicione pessoas, itens com precos e marque quem consumiu o que — o app calcula automaticamente quanto cada um deve pagar.

## Funcionalidades

- Adicionar/remover pessoas e itens
- Marcar quem consumiu cada item (checkboxes individuais, por coluna ou todos)
- Calculo automatico do valor por pessoa
- Colar itens direto do Excel (tab ou ponto-e-virgula)
- Importar/exportar dados em JSON
- Salvamento automatico (servidor + localStorage como fallback)

## Como rodar

```bash
php -S localhost:8000
```

Abra `http://localhost:8000` no navegador. Sem dependencias externas.

## Stack

- **Backend:** PHP com persistencia em JSON (`data.json`)
- **Frontend:** HTML, CSS e JavaScript vanilla — tudo em um unico arquivo (`index.php`)
