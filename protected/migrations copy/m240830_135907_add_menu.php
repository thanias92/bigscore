<?php

use yii\db\Migration;

/**
 * Class m240830_135907_add_menu
 */
class m240830_135907_add_menu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->batchInsert(
            '{{%menu}}',
            ['id', 'name', 'parent', 'route', 'order', 'data'],
            [
              ['1','Home','','/default/index','1','<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAACxAAAAsQHGLUmNAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAppJREFUSInFlV9IU1Ecx79nuxXb1e3O1BUTndEslQWK+DAqUQQlIoihklSkD0ovIhVokBBUKERFDxF76aUH0bAwEBRlgcRoaA0E28LRnCu96qpJ7l7TzdNDbDjv9Q8y6QMXfvd7zvl+748f3EP0RZWVVJv9kh5kOYAiORCQ1XCILM1cIZnljTxb81SfJOcEwr0tPEPYdA0AiJ9HQKMRqM3VW9ayJuN9WAv6ErQD6blgS6wgbAbHgIIAgDAxBErXoTZXb1nLseIbB1d1O0ELDXWCLbECAJiYqD5VhZ1qOQ7lFGFp5KFEixEPUBVUYqdajpTSWqC0dst1RvQ6wNifbGuyV0SvA0zWCTNvabhl3I8Ah22OV+yH8Ub+XwDvduLd83bwbmfyA746BzE2Ooz1cw/wccyJqfdv9xzAbBbc9h74giJST55BxkATFguvYmrWh4i9B/kVdfF93tE+LM1NSwy1R404ftYq38HEwAtMLzNI5Q6jTBhG97NOnFd+gEajgV9UwdVvi+8NeFzQVd2QPAGPS76DX343ZkJr0Gn/oEa/gMb6FgBAa3MDuO4+vApw+PabInfGDS47HwaTGfzrDkkHBpNZPkCbZUIa7Ueh+AWN9e0Jm65dsmLybhcmiR4agwkAkFdeh7zyOuxEPEChZGC53I7IwL+vutjUBkF1BOqVebyxdYFVs7DUtMUP7nYGkiHHEDVGrFbcAbHfl10PeFzIqb0n0f29HdsHhAVB1jAsCNBueN/tDEjBhes+S/MjY0xY+DSIkHsUYgSIUkBJABUD6ArKkFG0/a97Mw7bzWlJB5nF1cgslr9c9oICNFkXvQyUQiEEZxf3y18Ifl8kuuzC01rDsccpemMaUSqT0g6NRsnyvO/nD7+39S9ZfvzPlLLa3AAAAABJRU5ErkJggg== " width="24" height="24">'],
              ['2','Core','','/core/default/index','3','<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAACxAAAAsQHGLUmNAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAABKtJREFUSIm1VXtsU1UY//X29t7b14p7tR1sHZN13SPrhlQwPIQJkriJipuiCVO2LGGCCMo0mzFBgRCUh6IGIhO3bIwxkGdQjCGEMRwWMscGNLVubi2027qOMena9XX8Azq7rQIx+EtuTu73ne/3O+c733cO8D+DN+6fUau4wikxVK5YSEXwKN5Ij8PX1d7hqnG5sBAAHwANwH1vbANw5KGUkuO5eS/Nl7Rerk70EL2GBL/qDUrywjzp0BMarvaeQChmA9gGQHhf8sdVXE75m1HmIKnpSJLvnVcju8vfiBr4qkxOiF5Drh2c6puTLToRJjwawI778YsKcqTtQXLrD9OINpk9AkDEp1C1ZlmkIehr/EY18mSGsCwkVghAB2ARgBfDkVPpScyazW/FpAUNpy44b10xjVQAyPQHcLTVOHIl6JubJWRUcnoFAB4TqUgXa/P08pUHG8XavB2g+OEFEhSC+ckJDBU0zEhlI1JVzMsAMgFciRAhKTQgb45ExRfJiljN4uOxJbUZosznuNiS2gzxjPwcTqmeO16APytDuHrpAumUoEERRVNSMZVp6fNlzJ8uKti5Tp4tk/JHq80fgOBQ16xZjy3fG0/8I3D+UjUomJzOSaYvlXkd5hyfc7CTOB3G0R34/QiMVy3MlUVfrk5Mr9sUl5WgFFChPq+PQDpzWWzAPQR/wypr5WvJEeTwKmvAPYTIpZtVEU+9vkcQry0aFbA5fH2EhMteeJw1iODhlIQ+ttZ6avdHcTHRUdSxXRVxwhNrrf6hXjLp2XeVsrlFW1lVVhkA8MELuKYlsHnqBIZ5EDkhwNt7GSJy/Gn7/usNcevXr7fU1NQ01dfXx9Ts2R77c+Um23CsViTULJDwRZNm+AducPzBv4jxRp93Yf4zEVNZwfjGHouth0DaLPKBo99+LjcYDKirq3vPZDJZPB7PcGJiYuaGstXSM/s29Q6IElku5WmJu/PXSAoAzv/meiX/g5stPQ7fvyZr11GfZ9+P1O3vdn0aZbfbYTAY0NvbuxGASalUSlNSUkDTNPZ/sVEZ0/hxn+PA2g6P9frBYOu7Om569+uvuReueF42BQDOtw5DpRAAALZUO25+eaBvTXRU/CKXyyW2WCzQ6XQoKCgQsyy7qLi4WKvT6SgA4PF4uN6qt+tPVqX4b9saQyvE6fUH/hhy3i2qM5eGRx2WXt8ds81Tm5qaOlRaWorCwkIQHh/1p5soj1gh/+niVdpo6hi/aT8AjCnBi+3udSs+sV0Nc7gTUret+jga1WW4kFqOc+oyfFZ1LGxqxwioJtPT0pIYKQDo0rhRe4DcXc0YUZoFj6IBADyKBqHZBwtIhdSCxTPFsQCQO1vyj0Dgbqc4HI7BsCzjYLfbR+eNud/7BvxNv5u9+UVLZIpQ+8nGOz0tRvcehmHMzc3N6S0tLc6WS3qGvmPzUV1NXqqryetoOztyo9Nobmho6O7s7Kzo7++fcCjQqrktTZUqV+iDQ/QasmSe5NzDrDwcxqTI2uPet/vwLWvo1bF9/4DNaPZu+a8CE5AQR89+f3lkN9FrSOWHiv7sFHblIyMPIiOJzc2bIz6dpeZKHjn5o8bfovrlo6SDojgAAAAASUVORK5CYII=" width="24" height="24">'],
              ['3','Tenant','','/tenant/index','2','<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAACxAAAAsQHGLUmNAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAApJJREFUSIm1VE1ME0EYfbO7dZvtyi5FpVEipSaEv7AIVjERg0kDAiUQD8YLRg9eRA+A2njRkhgNmHjx0Iu3etAY0RPR6EUOGBSMEhSIqOjFaAqtSu0P3R0PTWs30uga9l1m53vfey/zswPkQrRX866mGd61bwaivXpduFxYHFUBx8AUdQxMUYujKrAeHFfubvZohaVBlRPl0NyEhbAWAIBVEE6UtPceyzQa5djUSoQJf+zhNNl5U+2+XgwALBkCVZPp5MqDLDp8bMbEKKcCDoz0BjkqbirINNoauvFt9BoAQGrv123f/3DUtkUmrq4zMdp60QoTQB4OxhkzjHNhegBH5h+kFDlmivn0/JMU19S4K+T3nxXNCPD7oyEut0ApxcjoI7ycWwQ1aEYA1FeWobvNA0JItq4LOD8cwNOCFjAVR4Ccpn8CpRhbnMCz4QCu+E5my7pDfrNMwJTuMW4OAISAcTbi9ZJeq1tBUk2PK5N3kPz0CjSVQNHhq1i61Q9iFWFTOhF/Nw7e1YjEwjiklj5ERocQm30M+6HL4MvcSGr63DWv6eqXt2DFot/zrwtghUKAYaElooi+uAf1ZxgAILf7YK04AL7MvebC8v4HWiwCQfECAHjXbkgtfeBL6wEAkuc0opN380nzB2R2z6Z4Udg1CLoaTweUKFi+fwGJD89hU7zg5G3YfPxGVidUt/7hkYHuDAqwgjiADSW1aWFNWijUdUKo69QJrTv2Zr95Z0OOx4/8Aed62jAc9CFqsYMwLIyAaipsq2H4jnbkD2hQanBbqTFk/DeY/tiR4vKdcblyP2+GeWR2LMFtdNaGk62XHGYESNFTYVLubvZo0vYgpK0SYVijb9yaoJpKmO+fI6nQ+55fG6fohWlNxOAAAAAASUVORK5CYII=" width="24" height="24">']
            ]
      );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240830_135907_add_menu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240830_135907_add_menu cannot be reverted.\n";

        return false;
    }
    */
}
