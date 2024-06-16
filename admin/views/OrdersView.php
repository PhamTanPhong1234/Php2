<!-- load file layout chung -->
<?php $this->layoutPath = "Layout.php"; ?>
<div class="col-md-12">
    <div style="display:flex;height: 30px;justify-content: space-between;align-items: center;margin-bottom: 30px;">
        <div>
            <h2 style="text-align: center">Đơn hàng</h2>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">List Orders</div>
        <div class="panel-body">
            <table class="table table-bordered table-hover">
                <tr>
                    <th>Tên</th>
                    <th>Số điện thoại</th>
                    <th>Ngày đặt</th>
                    <th style="width:150px; text-align: center;">Trạng thái</th>
                    <th style="width:150px;">Vận chuyển</th>
                </tr>
                <?php foreach ($listRecord as $rows) : ?>
                    <?php
                    //lay ban ghi customer
                    $customer = $this->modelGetCustomers($rows->customer_id);
                    ?>
                    <tr>
                        <td><?php echo $customer->name; ?></td>
                        <td><?php echo $customer->phone; ?></td>
                        <td>
                            <?php
                            $date = Date_create($rows->create_at);
                            echo Date_format($date, "d/m/Y H:i:s");
                            ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($rows->status == 1) : ?>
                                <span class="label label-success">Đã giao hàng</span>
                            <?php else : ?>
                                <span class="label label-danger">Chưa giao hàng</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <a href="index.php?controller=orders&action=detail&id=<?php echo $rows->id; ?>" class="label label-success">Chi tiết</a>
                            <?php if ($rows->status == 0) : ?>
                                <a href="index.php?controller=orders&action=delivery&id=<?php echo $rows->id; ?>" class="label label-info">Giao hàng</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <style type="text/css">
                .pagination {
                    padding: 0px;
                    margin: 0px;
                }
            </style>

        </div>
    </div>
</div>