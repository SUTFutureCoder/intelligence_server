#-*- encoding : utf-8 -*-
import jieba
import jieba.posseg as pseg

seg_list = jieba.cut("我来自沈阳工业大学", cut_all = True)
print "Full Mode:", "/" . join(seg_list)

seg_list = jieba.cut("我来到沈阳工业大学", cut_all = False)
print "Default Mode:", "/" . join(seg_list)

seg_list = jieba.cut("他来到了网易杭研大厦")
print ", " . join(seg_list)

seg_list = jieba.cut_for_search("小明硕士毕业于中国科学院计算所，后在日本京都大学深造")
print ", " . join(seg_list)

words = pseg.cut("北大")
for w in words:
    print w.word, w.flag