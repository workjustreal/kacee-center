<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class OrdersLazadaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithTitle, WithMapping, WithCustomCsvSettings
{
    protected $data;
    protected $title;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($data, $title)
    {
        $this->data = $data;
        $this->title = $title;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->data);
    }
    public function map($data): array
    {
        return [
            '',
            '',
            '',
            '',
            $data['sku'],
            '',
            '',
            '',
            $data['order_number'],
            '',
            '',
            $data['customer_name'],
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $data['sale_price'],
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $data['tracking_number'],
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings() :array
    {
        return [
            'orderItemId',
            'orderType',
            'deliveryType',
            'lazadaId',
            'sellerSku',
            'lazadaSku',
            'createTime',
            'updateTime',
            'orderNumber',
            'invoiceRequired',
            'invoiceNumber',
            'customerName',
            'nationalRegistrationNumber',
            'shippingName',
            'shippingAddress',
            'shippingAddress2',
            'shippingAddress3',
            'shippingAddress4',
            'shippingAddress5',
            'shippingPhone',
            'shippingPhone2',
            'shippingCity',
            'shippingPostCode',
            'shippingCountry',
            'shippingRegion',
            'billingName',
            'billingAddr',
            'billingAddr2',
            'billingAddr3',
            'billingAddr4',
            'billingAddr5',
            'billingPhone',
            'billingPhone2',
            'billingCity',
            'billingPostCode',
            'billingCountry',
            'taxCode',
            'branchNumber',
            'taxInvoiceRequested',
            'payMethod',
            'paidPrice',
            'unitPrice',
            'shippingFee',
            'walletCredit',
            'itemName',
            'variation',
            'cdShippingProvider',
            'shippingProvider',
            'shipmentTypeName',
            'shippingProviderType',
            'cdTrackingCode',
            'trackingCode',
            'trackingUrl',
            'shippingProviderFM',
            'trackingCodeFM',
            'trackingUrlFM',
            'promisedShippingTime',
            'premium',
            'status',
            'buyerFailedDeliveryReturnInitiator',
            'buyerFailedDeliveryReason',
            'buyerFailedDeliveryDetail',
            'buyerFailedDeliveryUserName',
            'bundleId',
            'bundleDiscount',
            'refundAmount',
            'sellerNote',
        ];
    }
    public function title(): string
    {
        return $this->title;
    }
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'enclosure' => '',
            'escape_character' => '\\',
            'contiguous' => false,
            'use_bom' => true,
            'input_encoding' => 'UTF-8'
        ];
    }
}