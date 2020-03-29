<?php

namespace App\Exports;

use App\Models\Trading;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

/**
 * 采购列表导出
 *
 * Date: 2020/3/8
 * @author George
 * @package App\Exports
 */
class PurchasingListExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
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
     * Date: 2020/3/29
     * @return Trading[]|Builder[]|Collection
     * @author George
     */
    public function collection()
    {
        $query = Trading::leftJoin('commodities', 'tradings.commodity_id', '=', 'commodities.id');
        $tradings = $query->select([
            'commodities.brand', 'commodities.name', 'commodities.specification', 'tradings.amount', 'tradings.price', 'tradings.total'
        ])->where('tradings.order_id', $this->order_id)->get();
        $tradings->push(['总计', null, null, null, null, $tradings->sum('total')]);

        return $tradings;
    }

    /**
     * Date: 2020/3/29
     * @return array
     * @author George
     */
    public function columnFormats(): array
    {
        return [
            'B' => DataType::TYPE_STRING,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER_00,
            'F' => NumberFormat::FORMAT_NUMBER_00
        ];
    }
}
