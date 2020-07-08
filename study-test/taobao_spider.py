import json
import os
import parsel
from  selenium import webdriver
from selenium.webdriver import ChromeOptions, ActionChains
from pyquery import PyQuery as pq
import wx
import time
from lxml import etree


class taoBaospider():
    def __init__(self):
        self.headers={
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36'
        }
        self.option = ChromeOptions()
        self.option.add_experimental_option('excludeSwitches', ['enable-automation'])    #浏览器的开发者模式
        self.option.add_argument("--disable-infobars")
        # self.option.add_argument('--proxy-server=http://127.0.0.1:9999')   #中间人代理 可不用
        self.option.add_argument('--no-sandbox')
        #self.option.add_experimental_option("prefs", {"profile.managed_default_content_settings.images": 2})  # 不加载图片,加快访问速度
        #self.option.add_argument('--headless')    # 无头浏览器
        self.driver = webdriver.Chrome(options=self.option)
    def Spider_Gui(self):     #图形化界面
        app = wx.App()
        frame = wx.Frame(None, title="Tao Bao Spider", pos=(1000, 200), size=(500, 400))
        zhanhao = wx.StaticText(frame, -1, "输入账号:", pos=(7, 9), )
        self.zhanghao = wx.TextCtrl(frame, pos=(90, 5), size=(300, 24)) #输入账号

        mima = wx.StaticText(frame, -1, "输入密码:", pos=(7, 54), )
        self.mima = wx.TextCtrl(frame, pos=(90, 50), size=(300, 24))     #输入密码

        # save = wx.StaticText(frame, -1, "存储路径:", pos=(7, 102), )
        # self.save = wx.TextCtrl(frame, pos=(90, 99), size=(300, 24))    #输入存储路径

        open_button = wx.Button(frame, label="开始抓取", pos=(180, 150), size=(100, 40))
        open_button.Bind(wx.EVT_BUTTON, self.Spider)                                  #为抓取按钮open_button添加事件

        frame.Show()    #显示图形化界面
        app.MainLoop()
    def Spider(self,event):  #爬虫程序
        self.driver.get('https://i.taobao.com/my_taobao.htm')
        self.driver.maximize_window()
        self.driver.find_element_by_xpath('//input[@id="fm-login-id"]').send_keys(self.zhanghao.GetValue())  #在网页中输入账号
        self.driver.find_element_by_xpath('//input[@id="fm-login-password"]').send_keys(self.mima.GetValue())  #网页中输入密码
        #滑动验证
        self.huadong = self.driver.find_element_by_xpath('//span[@class="nc_iconfont btn_slide"]')
        time.sleep(2)
        ActionChains(self.driver).click_and_hold(self.huadong).perform()
        time.sleep(1)
        ActionChains(self.driver).move_by_offset(xoffset=115,yoffset=0).perform()
        time.sleep(0.5)
        ActionChains(self.driver).move_by_offset(xoffset=180, yoffset=0).perform()
        ActionChains(self.driver).release().perform()
        time.sleep(1)
        self.driver.find_element_by_xpath('//button[@class="fm-button fm-submit password-login"]').click()#点击登录
        self.driver.implicitly_wait(20)  #隐式等待
        self.crawl_good_buy_data()
        # self.get_shoucang_item()
        # self.get_footmark_item()

    def crawl_good_buy_data(self):  #由于只是为了测试 ，所以我们只抓取第一页 ---我已买到的宝贝商品数据
         self.driver.find_element_by_xpath('//a[@id="bought"]').click()
         self.driver.implicitly_wait(20)
         time.sleep(3)
         html = self.driver.page_source
         print(html)
         doc = pq(html)
    # # 存储该页已经买到的宝贝数据
         good_items = doc('#tp-bought-root .js-order-container').items()

    # 遍历该页的所有宝贝
         for item in good_items:
            data_list = []
            good_time_and_id = item.find('.bought-wrapper-mod__head-info-cell___29cDO').text().replace('\n',"").replace('\r', "")
            good_merchant = item.find('.seller-mod__container___1w0Cx').text().replace('\n', "").replace('\r',"")
            good_name = item.find('.ml-mod__media___2sZrj').text().replace('\n', "").replace('\r', "")
            data_list.append(good_time_and_id)
            data_list.append(good_merchant)
            data_list.append(good_name)
            file_path = "./taobao_cookies.json"
            json_str = json.dumps(data_list)
            with open(file_path, 'a+') as f:
                f.write(json_str+'\n')

    # 收藏宝贝---由于只是为了测试 ，所以我们只抓取第一页
    def get_shoucang_item(self):
        self.driver.find_element_by_xpath('//dd[@id="favorite"]/a').click()
        self.driver.implicitly_wait(20)
        time.sleep(3)
        json_list = []
        html_str = self.driver.page_source
        obj_list = etree.HTML(html_str).xpath('//li')
        for obj in obj_list:
            item = {}
            item['title'] = ''.join([i.strip() for i in obj.xpath('./div[@class="img-item-title"]//text()')])
            item['url'] = ''.join([i.strip() for i in obj.xpath('./div[@class="img-item-title"]/a/@href')])
            item['price'] = ''.join([i.strip() for i in obj.xpath('./div[@class="price-container"]//text()')])
            if item['price'] == '':
                item['price'] = '失效'
            json_list.append(item)
        file_path = os.path.join(os.path.dirname(__file__) + '/shoucang_item.json')
        json_str = json.dumps(json_list)
        with open(file_path, 'a') as f:
            f.write(json_str)

    # 浏览足迹 ---由于只是为了测试 ，所以我们只抓取第一页
    def get_footmark_item(self):
        self.driver.find_element_by_xpath('//a[@data-spm="d1000391"]').click()
        self.driver.implicitly_wait(20)
        time.sleep(3)
        item_num = 0
        json_list = []
        html_str = self.driver.page_source
        obj_list = etree.HTML(html_str).xpath('//div[@class="item-list J_redsList"]/div')[item_num:]
        for obj in obj_list:
            item_num += 1
            item = {}
            item['date'] = ''.join([i.strip() for i in obj.xpath('./@data-date')])
            item['url'] = ''.join([i.strip() for i in obj.xpath('./a/@href')])
            item['name'] = ''.join([i.strip() for i in obj.xpath('.//div[@class="title"]//text()')])
            item['price'] = ''.join([i.strip() for i in obj.xpath('.//div[@class="price-box"]//text()')])
            json_list.append(item)
        file_path = os.path.join(os.path.dirname(__file__) + '/footmark_item.json')
        json_str = json.dumps(json_list)
        with open(file_path, 'w') as f:
            f.write(json_str)

if __name__ == '__main__':
    t = taoBaospider()
    t.Spider_Gui()
