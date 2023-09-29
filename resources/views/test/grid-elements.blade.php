<!DOCTYPE html>
<html>
<head>
<style>
.grid-container {
    width: 104mm;
    display: grid;
    gap: 2px 4px;
    grid-template-columns: auto auto auto;
    background-color: #cccccc;
    padding: 6px 8px;
    margin: 10px auto;
}
.grid-item {
    width: 32mm;
    height: 16mm;
    display: flex;
    background-color: rgba(255, 255, 255, 1);
    /* border: 1px solid rgba(0, 0, 0, 0.8); */
    padding: 0px 2px;
    font-size: 14px;
    align-items: center;
}
.qrcode {
    background-color: #f321e2;
    width: 12mm !important;
    height: 12mm !important;
    margin-left: 4px;
    margin-right: 4px;
}
.content {
    display: flex;
    flex-wrap: wrap;
    align-content: space-around;
    white-space: normal;
    width: 18mm !important;
    height: 14mm !important;
    margin: 0px;
    padding: 0px;
}
.content-top {
    width: 18mm !important;
    overflow-wrap: break-word;
    margin: 0px;
    padding: 0px;
}
.content-bottom {
    margin: 0px;
    padding: 0px;
}
.sku {
    font-size: 10px;
}
.lot {
    font-size: 11px;
}
.loc {
    font-size: 12px;
    font-weight: bold;
}
</style>
</head>
<body>

<div class="grid-container">
  <div class="grid-item">
    <div class="qrcode">@php echo DNS2D::getBarcodeSVG('4445645656|131516546|4545566', 'QRCODE', 1.8, 1.8) @endphp</div>
    <div class="content">
        <div class="content-top">
            <div class="sku">ACBDEFG1234567</div>
        </div>
        <div class="content-bottom">
            <div class="lot">123456789</div>
            <div class="loc">DV1A022</div>
        </div>
    </div>
  </div>
  <div class="grid-item">
    <div class="qrcode">@php echo DNS2D::getBarcodeSVG('4445645656|131516546|4545566', 'QRCODE', 1.8, 1.8) @endphp</div>
    <div class="content">
        <div class="content-top">
            <div class="sku">ACBDEFG1234567</div>
        </div>
        <div class="content-bottom">
            <div class="lot">123456789</div>
            <div class="loc">DV1A022</div>
        </div>
    </div>
  </div>
  <div class="grid-item">
    <div class="qrcode">@php echo DNS2D::getBarcodeSVG('4445645656|131516546|4545566', 'QRCODE', 1.8, 1.8) @endphp</div>
    <div class="content">
        <div class="content-top">
            <div class="sku">ACBDEFG1234567</div>
        </div>
        <div class="content-bottom">
            <div class="lot">123456789</div>
            <div class="loc">DV1A022</div>
        </div>
    </div>
  </div>
  <div class="grid-item">
    <div class="qrcode">@php echo DNS2D::getBarcodeSVG('4445645656|131516546|4545566', 'QRCODE', 1.8, 1.8) @endphp</div>
    <div class="content">
        <div class="content-top">
            <div class="sku">ACBDEFG1234567</div>
        </div>
        <div class="content-bottom">
            <div class="lot">123456789</div>
            <div class="loc">DV1A022</div>
        </div>
    </div>
  </div>
  <div class="grid-item">
    <div class="qrcode">@php echo DNS2D::getBarcodeSVG('4445645656|131516546|4545566', 'QRCODE', 1.8, 1.8) @endphp</div>
    <div class="content">
        <div class="content-top">
            <div class="sku">ACBDEFG1234567</div>
        </div>
        <div class="content-bottom">
            <div class="lot">123456789</div>
            <div class="loc">DV1A022</div>
        </div>
    </div>
  </div>
  <div class="grid-item">
    <div class="qrcode">@php echo DNS2D::getBarcodeSVG('4445645656|131516546|4545566', 'QRCODE', 1.8, 1.8) @endphp</div>
    <div class="content">
        <div class="content-top">
            <div class="sku">ACBDEFG1234567</div>
        </div>
        <div class="content-bottom">
            <div class="lot">123456789</div>
            <div class="loc">DV1A022</div>
        </div>
    </div>
  </div>
</div>

</body>
</html>


