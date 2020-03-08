<?php

namespace App\Exports;

use App\Models\Trading;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * 采购列表导出
 *
 * Date: 2020/3/8
 * @author George
 * @package App\Exports
 */
class PurchasingListExport implements FromQuery, WithHeadings
{
    use Exportable;

    /**
     * PurchasingListExport constructor.
     * @param int $order_id
     */
    public function __construct(int $order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * 定义表头
     *
     * Date: 2020/3/8
     * @return array
     * @author George
     */
    public function headings(): array
    {
        return [
            '品牌',
            '名称',
            '规格',
            '数量',
            '单价',
            '总价'
        ];
    }

    /**
     * @return Trading|Builder
     */
    public function query()
    {
        $query = Trading::leftJoin('commodities', 'tradings.commodity_id', '=', 'commodities.id');
        $query->select([
            'commodities.brand', 'commodities.name', 'commodities.specification', 'tradings.amount', 'tradings.price', 'tradings.total'
        ])->where('tradings.order_id', $this->order_id);
        return $query;
    }
}
