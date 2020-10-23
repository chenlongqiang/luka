from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.wait import WebDriverWait

browser = webdriver.Chrome()
try:
    browser.get('https://www.taobao.com')
    lis = browser.find_elements_by_css_selector('.service-bd li a')
    print(lis)
finally:
    browser.close
