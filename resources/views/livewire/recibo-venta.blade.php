<div style="font-family: 'Arial', sans-serif; font-size: 12px; margin: 10px;">
    @if ($ventaModel)
        <h1 style="font-size: 16px; text-align: center; font-weight: bold; margin-bottom: 10px;">Recibo de Venta</h1>
        <p style="font-size: 12px; margin-bottom: 5px;">No. Factura: EPS{{ $ventaModel->numero_factura }}</p>
        <p style="font-size: 12px; margin-bottom: 5px;">Fecha: {{ $ventaModel->created_at->format('d/m/Y H:i:s') }}</p>
        <p style="font-size: 12px; margin-bottom: 5px;">Cliente: {{ $ventaModel->cliente->nombre ?? 'Super Usuario' }}
        </p>

        <!-- Lista de productos -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 3px;">Producto</th>
                    <th style="text-align: right; padding: 3px;">Cant.</th>
                    <th style="text-align: right; padding: 3px;">Precio U.</th>
                    <th style="text-align: right; padding: 3px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($carritoRecibo as $producto)
                    <tr wire:key='{{ $producto->id }}'>
                        <td style="text-align: left; padding: 3px;">{{ $producto->producto->nombre ?? '' }}</td>
                        <td style="text-align: right; padding: 3px;">{{ $producto->cantidad }}</td>
                        <td style="text-align: right; padding: 3px;">
                            ${{ number_format($producto->producto->precio, 0, ',', '.') }}</td>
                        <td style="text-align: right; padding: 3px;">
                            ${{ number_format($producto->producto->precio * $producto->cantidad, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p style="margin-top: 10px; font-size: 12px;">Total: <span style="font-weight: bold; font-size: 14px;">
                ${{ number_format($ventaModel->total, 0, ',', '.') }}</span></p>
        <p style="font-size: 12px; margin-bottom: 5px;">Observaciones: {{ $ventaModel->observaciones }}</p>
    @else
        <p style="font-size: 12px; text-align: center; font-weight: bold; margin-top: 10px;">No se encontraron ventas
        </p>
    @endif
</div>
