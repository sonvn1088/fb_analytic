<?php

namespace App\Console\Commands;

use App\Helps\Facebook;
use App\Helps\General;
use App\Models\Account;
use App\Models\Browser;
use Illuminate\Console\Command;
use App\Models\MyPage;
use Illuminate\Support\Arr;

class SharePosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'share:posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Share posts to pages';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /*$myPages = MyPage::where('status', 1)->get();
        foreach($myPages as $myPage){
            $result = Facebook::checkToken($myPage->token);
           if(isset($result['id']))
                $myPage->sharePosts();
           else{
                $editor = $myPage->editor();
                if($editor){
                    $editor->status = 0;
                    $editor->save();
                    $editor->error_message = Arr::get($result, 'error.message');
                    General::sendMail($editor);
                }
            }

        }*/
        $array = [
            ['zhao.zi_long@yahoo.com', 'Giang H? Hi?m Ác', '09/20/1998', '100003239220217', 'M8GT3HNj', 'ZGUFmgH5'],
            ['dinhkheo@yahoo.com', 'ThôngBáo G?i B?n', '06/15/1998', '100003833166789', 'hR9EPFAu', 'w5YBdFpn'],
            ['hiencandy334@yahoo.com', 'Nguy?n Ng?c', '05/26/1996', '100005179001012', 'e2hi2xAE', 'pMEsjSJ3'],
            ['onthidaihoc3@yahoo.com', 'Ôn Thi ??i H?c', '04/26/1990', '100006507172353', 'Run988S9', 'RPnmNTt2'],
            ['drbi147x@yahoo.com', 'Minions Rush', '01/20/1992', '100006536832123', '3scbwCDX', 'u6Zrf3Le'],
            ['phong147x@yahoo.com', 'PGonly Phong', '09/20/1998', '100008184823119', 'fs8PcBTr', 'K3CR3MpB'],
            ['rinthadwyp@yahoo.com', 'RienTha Viindy', '01/04/1995', '100004902734202', 'kpZA5x4U', 'K2kLgHNa'],
            ['tonghoangcuongnb2002@yahoo.com', 'Ph?m Ng?c Hùng', '09/13/1997', '100006714708205', 'L58Q2JFL', 'CB8x07cL'],
            ['tam.minh946@yahoo.com', 'Minh Tam', '03/09/1990', '100007866724685', 'srF1NmAe', 'LZU4jO2Y'],
            ['lethuy.1998@yahoo.com', 'Su Ng?', '06/25/1998', '100005446174841', 'pAYl9847', '0TATJ4WH'],
            ['nhuy120511@yahoo.com', 'S? Nh? İ', '05/12/2000', '100007936242843', '87K9FMFG', 'pn99LJxW'],
            ['lethanhlam1996@yahoo.com', 'Xíu Camaro', '01/16/1996', '100008156613608', 'jRd15Exl', 'wOI5xRja'],
            ['thienhau3090@yahoo.com', 'V?n Thiên H?u', '04/30/1987', '100006605552233', 'BFYR1m9A', '1f7TyTa6'],
            ['bjndajka1@yahoo.com', 'Bin Bin', '05/05/1990', '100005010032028', '1sIDEfmC', '7coKRlWD'],
            ['thaobop95@yahoo.com', 'Th?o B?p', '02/14/1995', '100004351841270', '', ''],
            ['trinhhung547@yahoo.com', 'Trinh Hung', '10/10/1996', '100004896489209', '', ''],
            ['letinh.manhduong@yahoo.com', 'Tình Lê', '03/15/1988', '100005144319537', '', ''],
            ['ssu956@yahoo.com', 'Em Tên Su', '', '100005170007928', '', ''],
            ['trananh199890@yahoo.com', 'Kho?ng L?ng', '04/05/1998', '100005501504837', '', ''],
            ['myankute@yahoo.com', 'Nkóx Ngáo Ng?', '01/03', '100005749639287', '', ''],
            ['em_lun@yahoo.com', '? Tao Th? ??y', '08/01/1997', '100006187953621', '', ''],
            ['b.buon10@yahoo.com', 'Nhok Sad', '05/23/2001', '100006315760101', '', ''],
            ['tong.lynk@yahoo.com', 'Lynk Sój', '01/10/1997', '100006450895407', '', ''],
            ['dinhthilan24@yahoo.com', 'Dang Linh', '12/24/1997', '100006749912107', '', ''],
            ['tuantuan2406@yahoo.com', 'Tu?n T?m T?m', '06/24/2000', '100007167634989', '', ''],
            ['langvnb@yahoo.com', 'Góc C??i', '11/03/1998', '100007619225449', '', ''],
            ['anhchuot_scyv@yahoo.com', 'H?nh Phúc Là ?âu', '08/05', '100008186190269', '', ''],
        ];

        foreach($array as $item){
            $account = Account::where('fb_id', $item[3])->first();
            $browser = Browser::inRandomOrder()->first();
            $account->browser_id = $browser->id;
            $account->save();
        }
    }
}
