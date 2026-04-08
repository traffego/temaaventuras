# Tema Aventuras 🌊

Tema WordPress moderno para empresas de turismo de aventura — rafting, trilhas, tirolesa e mais.

**Versão:** 1.0.0 | **Requer:** WordPress 6.0+ | **PHP:** 8.0+

---

## ✅ Instalação

1. Faça upload da pasta `temaaventuras` em `wp-content/themes/`
2. Ative o tema em **Aparência → Temas**
3. Vá em **Aventuras → 💳 Pagamento** e insira suas credenciais do Mercado Pago

---

## 📄 Páginas a criar no WordPress

| Slug sugerido | Template | Descrição |
|---|---|---|
| `/reservar` | **Checkout – Reserva e Pagamento** | Fluxo de pagamento (PIX e Cartão) |
| `/confirmacao-reserva` | **Confirmação de Reserva** | Página pós-pagamento |
| `/minha-reserva` | **Minha Reserva** | Consulta de status pelo cliente |
| `/atividades` | **Atividades – Listagem** | Grade de todas as atividades |
| `/sobre` | **Sobre Nós** | Página institucional |
| `/contato` | **Fale Conosco** | Formulário de contato |

---

## 🏗️ Custom Post Types (CPTs)

| CPT | Slug | Descrição |
|---|---|---|
| Atividade | `atividade` | Modalidades (Rafting, Trilha…) |
| Pacote | `pacote` | Combos de atividades |
| Depoimento | `depoimento` | Avaliações de clientes |
| Sessão | Interno | Gerenciado dentro de cada Atividade |
| Reserva | Interno | Criado automaticamente no checkout |

---

## 💳 Módulo de Pagamentos (Mercado Pago)

### Configuração

1. Acesse **Aventuras → 💳 Pagamento**
2. Cole seu **Access Token** e **Public Key** (Sandbox para testes, Produção para cobranças reais)
3. Copie a **URL do Webhook** exibida e cadastre em [mercadopago.com.br/developers → Notificações IPN](https://www.mercadopago.com.br/developers/panel/notifications/ipn)
4. Selecione o tipo de notificação **Payments**

### Fluxo completo

```
Cliente → Página da Atividade (single-atividade.php)
       → Escolhe sessão na sidebar → Clica "Reservar Agora"
       → Checkout (3 etapas):
            1. Dados pessoais + inscritos (nome, CPF, telefone de cada um)
            2. Confirmação da sessão escolhida
            3. Pagamento (PIX com QR Code ou Cartão de Crédito)
       → Confirmação (page-confirmacao.php)
       → E-mail enviado ao cliente + admin
       → Webhook do MP atualiza status automaticamente
```

### Gestão de Sessões

Em cada Atividade (admin), use a meta box **"📅 Sessões Agendadas"**:

- **Data** — dia do evento (obrigatório)
- **Horário** — hora de início (obrigatório)
- **Vagas** — limite de participantes (obrigatório)
- **Preço / pessoa** — pode ser diferente do preço base da atividade
- **Observações** — ex: ponto de encontro, o que levar (opcional)

O sistema controla vagas automaticamente com base nas reservas **aprovadas**.

### Status de Reserva

| Status | Descrição |
|---|---|
| `pendente` | Criada, aguardando pagamento |
| `aprovado` | Pago — participante confirmado |
| `rejeitado` | Pagamento recusado |
| `cancelado` | Cancelamento manual ou expirado |

---

## 🗂️ Estrutura de Arquivos

```
temaaventuras/
├── functions.php                     # Bootstrap principal
├── style.css                         # Metadados + importações CSS
├── single-atividade.php              # Página da atividade com sidebar de sessões
├── single-pacote.php                 # Página do pacote
├── page-templates/
│   ├── page-checkout.php             # Checkout de reserva
│   ├── page-confirmacao.php          # Confirmação pós-pagamento
│   └── page-minha-reserva.php        # Consulta de reserva pelo cliente
├── inc/
│   ├── helpers.php                   # Funções utilitárias (ta_checkout_url, ta_proxima_sessao…)
│   ├── custom-post-types.php         # CPTs Atividade, Pacote, Depoimento
│   └── payment/
│       ├── admin-config.php          # Página admin + tema_aventuras_payment_config()
│       ├── class-mercadopago.php     # Wrapper da API REST do MP
│       ├── class-reservas.php        # CPTs Sessão + Reserva, meta boxes, CRUD
│       ├── webhook.php               # IPN handler + polling AJAX
│       ├── emails.php                # E-mails HTML transacionais
│       ├── ajax-checkout.php         # Handlers AJAX do checkout
│       └── views/
│           ├── config-page.php       # UI da página de configurações
│           └── reservas-page.php     # Listagem de reservas com CSV
└── assets/
    ├── css/
    │   ├── variables.css             # Tokens de design
    │   ├── base.css                  # Reset + tipografia
    │   ├── layout.css                # Grid + containers
    │   ├── components.css            # Botões, cards, badges
    │   ├── animations.css            # Micro-animações
    │   └── checkout.css              # Estilos do checkout
    ├── js/
    │   ├── main.js                   # JS global
    │   ├── navbar.js                 # Navbar responsiva
    │   ├── checkout.js               # MP CardForm + PIX polling + máscaras
    │   └── gallery.js                # Lightbox da galeria
    └── images/
        └── placeholder.jpg           # Placeholder SVG para imagens ausentes
```

---

## 🔧 Personalização (Customizer)

**Aparência → Personalizar → Identidade Visual da Aventura:**

- Cores: primária (verde), secundária (amarelo), terciária (azul), fundo, texto
- Logo e informações da empresa (WhatsApp, e-mail, redes sociais)
- Textos hero, números de estatísticas

---

## 📧 E-mails Transacionais

O sistema envia automaticamente:

- **Para o cliente:** confirmação com código, data, hora, lista de inscritos e link "Minha Reserva"
- **Para o admin:** notificação de nova reserva com todos os dados e link direto ao painel

> 💡 Para garantir entrega: instale **WP Mail SMTP** e configure um servidor SMTP (Gmail, SendGrid, etc.)

---

## 🧪 Testando em Sandbox

1. Configure o modo **Sandbox** ativo e use as credenciais de **Teste** do MP
2. Use CPF fictício: `12345678909`
3. Para PIX: aguarde 30 min ou use o painel sandbox para simular aprovação
4. Para Cartão: use os [cartões de teste do Mercado Pago](https://www.mercadopago.com.br/developers/pt/docs/your-integrations/test/cards)
5. Verifique o log em **Aventuras → 📋 Reservas**

---

## 📋 Lista de Participantes

Em **Aventuras → 📋 Reservas** você pode:

- Filtrar por atividade, status ou data
- Ver total de inscritos por sessão
- **Exportar CSV** com todos os dados (responsável + todos os inscritos por reserva)

---

*Desenvolvido com ❤️ por [Traffego](https://traffego.com.br)*
