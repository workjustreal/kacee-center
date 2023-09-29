<style>
    @font-face {
        font-family: "THSarabunNew";
        font-style: normal;
        font-weight: normal;
        src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
    }

    @font-face {
        font-family: "THSarabunNew";
        font-style: normal;
        font-weight: bold;
        src: url("{{ public_path('fonts/THSarabunNew Bold.ttf') }}") format('truetype');
    }

    @font-face {
        font-family: "THSarabunNew";
        font-style: italic;
        font-weight: normal;
        src: url("{{ public_path('fonts/THSarabunNew Italic.ttf') }}") format('truetype');
    }

    @font-face {
        font-family: "THSarabunNew";
        font-style: italic;
        font-weight: bold;
        src: url("{{ public_path('fonts/THSarabunNew BoldItalic.ttf') }}") format('truetype');
    }

    @page {
        margin: 3mm 5mm !important;
    }

    body {
        font-family: "THSarabunNew", sans-serif;
        font-size: 18px;
    }

    .text-decoration-dotted {
        display: inline-flex;
        height: 24px;
        border-bottom: 0.8px dotted #000;
        padding-bottom: 2px;
        width: fit-content;
    }
    .text-decoration-line-through {
        -webkit-text-decoration-line: line-through;
        text-decoration: line-through;
    }
    .rtv {
        position: relative;
    }
    .abs {
        position: absolute;
    }
    .full-underline {
        width: 100%;
    }
    .full-underline span.full-dotted {
        display: block;
        width: 100%;
        height: 26px;
        border-bottom: 0.8px dotted #000;
    }
    .line-height {
        line-height: 16px !important;
    }
    .table-leave {
        margin: 0px !important;
        padding: 0px !important;
    }
    .table-leave thead tr th,
    .table-leave tbody tr th,
    .table-leave tbody tr td {
        padding: 0px !important;
        line-height: 16px !important;
    }

    .text-start {
        text-align: left !important;
    }

    .text-end {
        text-align: right !important;
    }

    .text-center {
        text-align: center !important;
    }

    .logo-img {
        width: 70px;
    }

    .person-img {
        width: 80px;
        max-height: 100px;
    }

    .bg-white {
        background-color: #fff;
    }

    .column-three {
        float: left;
        width: 25%;
        padding: 0px;
        height: 20px;
        line-height: 14px;
    }

    .column-two {
        float: left;
        width: 50%;
    }

    .column-approve {
        float: left;
        width: 33.33%;
    }

    /* Clear floats after the columns */
    .row:after {
        content: "";
        display: table;
        clear: both;
    }

    .fw-bold {
        font-weight: bold;
    }

    .m-0 {
        margin: 0 !important;
    }
    .m-1 {
        margin: 0.375rem !important;
    }
    .m-2 {
        margin: 0.75rem !important;
    }
    .m-3 {
        margin: 1.5rem !important;
    }
    .m-4 {
        margin: 2.25rem !important;
    }
    .m-5 {
        margin: 4.5rem !important;
    }
    .m-auto {
        margin: auto !important;
    }
    .mx-0 {
        margin-right: 0 !important;
        margin-left: 0 !important;
    }
    .mx-1 {
        margin-right: 0.375rem !important;
        margin-left: 0.375rem !important;
    }
    .mx-2 {
        margin-right: 0.75rem !important;
        margin-left: 0.75rem !important;
    }
    .mx-3 {
        margin-right: 1.5rem !important;
        margin-left: 1.5rem !important;
    }
    .mx-4 {
        margin-right: 2.25rem !important;
        margin-left: 2.25rem !important;
    }
    .mx-5 {
        margin-right: 4.5rem !important;
        margin-left: 4.5rem !important;
    }
    .mx-auto {
        margin-right: auto !important;
        margin-left: auto !important;
    }
    .my-0 {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }
    .my-1 {
        margin-top: 0.375rem !important;
        margin-bottom: 0.375rem !important;
    }
    .my-2 {
        margin-top: 0.75rem !important;
        margin-bottom: 0.75rem !important;
    }
    .my-3 {
        margin-top: 1.5rem !important;
        margin-bottom: 1.5rem !important;
    }
    .my-4 {
        margin-top: 2.25rem !important;
        margin-bottom: 2.25rem !important;
    }
    .my-5 {
        margin-top: 4.5rem !important;
        margin-bottom: 4.5rem !important;
    }
    .my-auto {
        margin-top: auto !important;
        margin-bottom: auto !important;
    }
    .mt-0 {
        margin-top: 0 !important;
    }
    .mt-1 {
        margin-top: 0.375rem !important;
    }
    .mt-2 {
        margin-top: 0.75rem !important;
    }
    .mt-3 {
        margin-top: 1.5rem !important;
    }
    .mt-4 {
        margin-top: 2.25rem !important;
    }
    .mt-5 {
        margin-top: 4.5rem !important;
    }
    .mt-auto {
        margin-top: auto !important;
    }
    .me-0 {
        margin-right: 0 !important;
    }
    .me-1 {
        margin-right: 0.375rem !important;
    }
    .me-2 {
        margin-right: 0.75rem !important;
    }
    .me-3 {
        margin-right: 1.5rem !important;
    }
    .me-4 {
        margin-right: 2.25rem !important;
    }
    .me-5 {
        margin-right: 4.5rem !important;
    }
    .me-auto {
        margin-right: auto !important;
    }
    .mb-0 {
        margin-bottom: 0 !important;
    }
    .mb-1 {
        margin-bottom: 0.375rem !important;
    }
    .mb-2 {
        margin-bottom: 0.75rem !important;
    }
    .mb-3 {
        margin-bottom: 1.5rem !important;
    }
    .mb-4 {
        margin-bottom: 2.25rem !important;
    }
    .mb-5 {
        margin-bottom: 4.5rem !important;
    }
    .mb-auto {
        margin-bottom: auto !important;
    }
    .ms-0 {
        margin-left: 0 !important;
    }
    .ms-1 {
        margin-left: 0.375rem !important;
    }
    .ms-2 {
        margin-left: 0.75rem !important;
    }
    .ms-3 {
        margin-left: 1.5rem !important;
    }
    .ms-4 {
        margin-left: 2.25rem !important;
    }
    .ms-5 {
        margin-left: 4.5rem !important;
    }
    .ms-auto {
        margin-left: auto !important;
    }
    .m-n1 {
        margin: -0.375rem !important;
    }
    .m-n2 {
        margin: -0.75rem !important;
    }
    .m-n3 {
        margin: -1.5rem !important;
    }
    .m-n4 {
        margin: -2.25rem !important;
    }
    .m-n5 {
        margin: -4.5rem !important;
    }
    .mx-n1 {
        margin-right: -0.375rem !important;
        margin-left: -0.375rem !important;
    }
    .mx-n2 {
        margin-right: -0.75rem !important;
        margin-left: -0.75rem !important;
    }
    .mx-n3 {
        margin-right: -1.5rem !important;
        margin-left: -1.5rem !important;
    }
    .mx-n4 {
        margin-right: -2.25rem !important;
        margin-left: -2.25rem !important;
    }
    .mx-n5 {
        margin-right: -4.5rem !important;
        margin-left: -4.5rem !important;
    }
    .my-n1 {
        margin-top: -0.375rem !important;
        margin-bottom: -0.375rem !important;
    }
    .my-n2 {
        margin-top: -0.75rem !important;
        margin-bottom: -0.75rem !important;
    }
    .my-n3 {
        margin-top: -1.5rem !important;
        margin-bottom: -1.5rem !important;
    }
    .my-n4 {
        margin-top: -2.25rem !important;
        margin-bottom: -2.25rem !important;
    }
    .my-n5 {
        margin-top: -4.5rem !important;
        margin-bottom: -4.5rem !important;
    }
    .mt-n1 {
        margin-top: -0.375rem !important;
    }
    .mt-n2 {
        margin-top: -0.75rem !important;
    }
    .mt-n3 {
        margin-top: -1.5rem !important;
    }
    .mt-n4 {
        margin-top: -2.25rem !important;
    }
    .mt-n5 {
        margin-top: -4.5rem !important;
    }
    .me-n1 {
        margin-right: -0.375rem !important;
    }
    .me-n2 {
        margin-right: -0.75rem !important;
    }
    .me-n3 {
        margin-right: -1.5rem !important;
    }
    .me-n4 {
        margin-right: -2.25rem !important;
    }
    .me-n5 {
        margin-right: -4.5rem !important;
    }
    .mb-n1 {
        margin-bottom: -0.375rem !important;
    }
    .mb-n2 {
        margin-bottom: -0.75rem !important;
    }
    .mb-n3 {
        margin-bottom: -1.5rem !important;
    }
    .mb-n4 {
        margin-bottom: -2.25rem !important;
    }
    .mb-n5 {
        margin-bottom: -4.5rem !important;
    }
    .ms-n1 {
        margin-left: -0.375rem !important;
    }
    .ms-n2 {
        margin-left: -0.75rem !important;
    }
    .ms-n3 {
        margin-left: -1.5rem !important;
    }
    .ms-n4 {
        margin-left: -2.25rem !important;
    }
    .ms-n5 {
        margin-left: -4.5rem !important;
    }
    .p-0 {
        padding: 0 !important;
    }
    .p-1 {
        padding: 0.375rem !important;
    }
    .p-2 {
        padding: 0.75rem !important;
    }
    .p-3 {
        padding: 1.5rem !important;
    }
    .p-4 {
        padding: 2.25rem !important;
    }
    .p-5 {
        padding: 4.5rem !important;
    }
    .px-0 {
        padding-right: 0 !important;
        padding-left: 0 !important;
    }
    .px-1 {
        padding-right: 0.375rem !important;
        padding-left: 0.375rem !important;
    }
    .px-2 {
        padding-right: 0.75rem !important;
        padding-left: 0.75rem !important;
    }
    .px-3 {
        padding-right: 1.5rem !important;
        padding-left: 1.5rem !important;
    }
    .px-4 {
        padding-right: 2.25rem !important;
        padding-left: 2.25rem !important;
    }
    .px-5 {
        padding-right: 4.5rem !important;
        padding-left: 4.5rem !important;
    }
    .py-0 {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
    .py-1 {
        padding-top: 0.375rem !important;
        padding-bottom: 0.375rem !important;
    }
    .py-2 {
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
    }
    .py-3 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
    }
    .py-4 {
        padding-top: 2.25rem !important;
        padding-bottom: 2.25rem !important;
    }
    .py-5 {
        padding-top: 4.5rem !important;
        padding-bottom: 4.5rem !important;
    }
    .pt-0 {
        padding-top: 0 !important;
    }
    .pt-1 {
        padding-top: 0.375rem !important;
    }
    .pt-2 {
        padding-top: 0.75rem !important;
    }
    .pt-3 {
        padding-top: 1.5rem !important;
    }
    .pt-4 {
        padding-top: 2.25rem !important;
    }
    .pt-5 {
        padding-top: 4.5rem !important;
    }
    .pe-0 {
        padding-right: 0 !important;
    }
    .pe-1 {
        padding-right: 0.375rem !important;
    }
    .pe-2 {
        padding-right: 0.75rem !important;
    }
    .pe-3 {
        padding-right: 1.5rem !important;
    }
    .pe-4 {
        padding-right: 2.25rem !important;
    }
    .pe-5 {
        padding-right: 4.5rem !important;
    }
    .pb-0 {
        padding-bottom: 0 !important;
    }
    .pb-1 {
        padding-bottom: 0.375rem !important;
    }
    .pb-2 {
        padding-bottom: 0.75rem !important;
    }
    .pb-3 {
        padding-bottom: 1.5rem !important;
    }
    .pb-4 {
        padding-bottom: 2.25rem !important;
    }
    .pb-5 {
        padding-bottom: 4.5rem !important;
    }
    .ps-0 {
        padding-left: 0 !important;
    }
    .ps-1 {
        padding-left: 0.375rem !important;
    }
    .ps-2 {
        padding-left: 0.75rem !important;
    }
    .ps-3 {
        padding-left: 1.5rem !important;
    }
    .ps-4 {
        padding-left: 2.25rem !important;
    }
</style>