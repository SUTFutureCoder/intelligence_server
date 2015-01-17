jieba
========
"结巴"中文分词：做最好的 Python 中文分词组件
"Jieba" (Chinese for "to stutter") Chinese text segmentation: built to be the best Python Chinese word segmentation module.
- _Scroll down for English documentation._


特点
========
* 支持三种分词模式：
    * 精确模式，试图将句子最精确地切开，适合文本分析；
    * 全模式，把句子中所有的可以成词的词语都扫描出来, 速度非常快，但是不能解决歧义；
    * 搜索引擎模式，在精确模式的基础上，对长词再次切分，提高召回率，适合用于搜索引擎分词。

* 支持繁体分词
* 支持自定义词典

在线演示
=========
http://jiebademo.ap01.aws.af.cm/

(Powered by Appfog)

网站代码：https://github.com/fxsjy/jiebademo


安装说明
=======
Python 2.x
-----------
* 全自动安装：`easy_install jieba` 或者 `pip install jieba`
* 半自动安装：先下载 http://pypi.python.org/pypi/jieba/ ，解压后运行 python setup.py install
* 手动安装：将 jieba 目录放置于当前目录或者 site-packages 目录
* 通过 `import jieba` 来引用

Python 3.x
-----------
* 目前 master 分支是只支持 Python2.x 的
* Python 3.x 版本的分支也已经基本可用： https://github.com/fxsjy/jieba/tree/jieba3k

```shell
git clone https://github.com/fxsjy/jieba.git
git checkout jieba3k
python setup.py install
```

* 或使用pip3安装： pip3 install jieba3k

算法
========
* 基于前缀词典实现高效的词图扫描，生成句子中汉字所有可能成词情况所构成的有向无环图 (DAG)
* 采用了动态规划查找最大概率路径, 找出基于词频的最大切分组合
* 对于未登录词，采用了基于汉字成词能力的 HMM 模型，使用了 Viterbi 算法

主要功能
=======
1) ：分词
--------
* `jieba.cut` 方法接受三个输入参数: 需要分词的字符串；cut_all 参数用来控制是否采用全模式；HMM 参数用来控制是否使用 HMM 模型
* `jieba.cut_for_search` 方法接受两个参数：需要分词的字符串；是否使用 HMM 模型。该方法适合用于搜索引擎构建倒排索引的分词，粒度比较细
* 注意：待分词的字符串可以是 GBK 字符串、UTF-8 字符串或者 unicode
* `jieba.cut` 以及 `jieba.cut_for_search` 返回的结构都是一个可迭代的 generator，可以使用 for 循环来获得分词后得到的每一个词语(unicode)，也可以用 list(jieba.cut(...)) 转化为 list

代码示例( 分词 )

```python
#encoding=utf-8
import jieba

seg_list = jieba.cut("我来到北京清华大学", cut_all=True)
print "Full Mode:", "/ ".join(seg_list)  # 全模式

seg_list = jieba.cut("我来到北京清华大学", cut_all=False)
print "Default Mode:", "/ ".join(seg_list)  # 精确模式

seg_list = jieba.cut("他来到了网易杭研大厦")  # 默认是精确模式
print ", ".join(seg_list)

seg_list = jieba.cut_for_search("小明硕士毕业于中国科学院计算所，后在日本京都大学深造")  # 搜索引擎模式
print ", ".join(seg_list)
```

输出:

    【全模式】: 我/ 来到/ 北京/ 清华/ 清华大学/ 华大/ 大学

    【精确模式】: 我/ 来到/ 北京/ 清华大学

    【新词识别】：他, 来到, 了, 网易, 杭研, 大厦    (此处，“杭研”并没有在词典中，但是也被Viterbi算法识别出来了)

    【搜索引擎模式】： 小明, 硕士, 毕业, 于, 中国, 科学, 学院, 科学院, 中国科学院, 计算, 计算所, 后, 在, 日本, 京都, 大学, 日本京都大学, 深造

2) ：添加自定义词典
----------------

* 开发者可以指定自己自定义的词典，以便包含 jieba 词库里没有的词。虽然 jieba 有新词识别能力，但是自行添加新词可以保证更高的正确率
* 用法： jieba.load_userdict(file_name) # file_name 为自定义词典的路径
* 词典格式和`dict.txt`一样，一个词占一行；每一行分三部分，一部分为词语，另一部分为词频，最后为词性（可省略），用空格隔开
* 范例：

    * 自定义词典：https://github.com/fxsjy/jieba/blob/master/test/userdict.txt

    * 用法示例：https://github.com/fxsjy/jieba/blob/master/test/test_userdict.py


        * 之前： 李小福 / 是 / 创新 / 办 / 主任 / 也 / 是 / 云 / 计算 / 方面 / 的 / 专家 /

        * 加载自定义词库后：　李小福 / 是 / 创新办 / 主任 / 也 / 是 / 云计算 / 方面 / 的 / 专家 /


* "通过用户自定义词典来增强歧义纠错能力" --- https://github.com/fxsjy/jieba/issues/14

3) ：关键词提取
-------------
* jieba.analyse.extract_tags(sentence,topK,withWeight) #需要先 `import jieba.analyse`
* sentence 为待提取的文本
* topK 为返回几个 TF/IDF 权重最大的关键词，默认值为 20
* withWeight 为是否一并返回关键词权重值，默认值为 False

代码示例 （关键词提取）

https://github.com/fxsjy/jieba/blob/master/test/extract_tags.py

关键词提取所使用逆向文件频率（IDF）文本语料库可以切换成自定义语料库的路径

* 用法： jieba.analyse.set_idf_path(file_name) # file_name为自定义语料库的路径
* 自定义语料库示例：https://github.com/fxsjy/jieba/blob/master/extra_dict/idf.txt.big
* 用法示例：https://github.com/fxsjy/jieba/blob/master/test/extract_tags_idfpath.py

关键词提取所使用停止词（Stop Words）文本语料库可以切换成自定义语料库的路径

* 用法： jieba.analyse.set_stop_words(file_name) # file_name为自定义语料库的路径
* 自定义语料库示例：https://github.com/fxsjy/jieba/blob/master/extra_dict/stop_words.txt
* 用法示例：https://github.com/fxsjy/jieba/blob/master/test/extract_tags_stop_words.py

关键词一并返回关键词权重值示例

* 用法示例：https://github.com/fxsjy/jieba/blob/master/test/extract_tags_with_weight.py

#### 基于TextRank算法的关键词抽取实现
算法论文： [TextRank: Bringing Order into Texts](http://web.eecs.umich.edu/~mihalcea/papers/mihalcea.emnlp04.pdf)

##### 基本思想:

1. 将待抽取关键词的文本进行分词
2. 以固定窗口大小(我选的5，可适当调整)，词之间的共现关系，构建图
3. 计算图中节点的PageRank，注意是无向带权图

##### 基本使用:
jieba.analyse.textrank(raw_text)

##### 示例结果:
来自`__main__`的示例结果：

```
吉林 1.0
欧亚 0.864834432786
置业 0.553465925497
实现 0.520660869531
收入 0.379699688954
增资 0.355086023683
子公司 0.349758490263
全资 0.308537396283
城市 0.306103738053
商业 0.304837414946
```

4) : 词性标注
-----------
* 标注句子分词后每个词的词性，采用和 ictclas 兼容的标记法
* 用法示例

```pycon
>>> import jieba.posseg as pseg
>>> words = pseg.cut("我爱北京天安门")
>>> for w in words:
...    print w.word, w.flag
...
我 r
爱 v
北京 ns
天安门 ns
```

5) : 并行分词
-----------
* 原理：将目标文本按行分隔后，把各行文本分配到多个 python 进程并行分词，然后归并结果，从而获得分词速度的可观提升
* 基于 python 自带的 multiprocessing 模块，目前暂不支持 windows
* 用法：
    * `jieba.enable_parallel(4)` # 开启并行分词模式，参数为并行进程数
    * `jieba.disable_parallel()` # 关闭并行分词模式

* 例子：https://github.com/fxsjy/jieba/blob/master/test/parallel/test_file.py

* 实验结果：在 4 核 3.4GHz Linux 机器上，对金庸全集进行精确分词，获得了 1MB/s 的速度，是单进程版的 3.3 倍。


6) : Tokenize：返回词语在原文的起始位置
----------------------------------
* 注意，输入参数只接受 unicode
* 默认模式

```python
result = jieba.tokenize(u'永和服装饰品有限公司')
for tk in result:
    print "word %s\t\t start: %d \t\t end:%d" % (tk[0],tk[1],tk[2])
```

```
word 永和                start: 0                end:2
word 服装                start: 2                end:4
word 饰品                start: 4                end:6
word 有限公司            start: 6                end:10

```

* 搜索模式

```python
result = jieba.tokenize(u'永和服装饰品有限公司',mode='search')
for tk in result:
    print "word %s\t\t start: %d \t\t end:%d" % (tk[0],tk[1],tk[2])
```

```
word 永和                start: 0                end:2
word 服装                start: 2                end:4
word 饰品                start: 4                end:6
word 有限                start: 6                end:8
word 公司                start: 8                end:10
word 有限公司            start: 6                end:10
```


7) : ChineseAnalyzer for Whoosh 搜索引擎
--------------------------------------------
* 引用： `from jieba.analyse import ChineseAnalyzer`
* 用法示例：https://github.com/fxsjy/jieba/blob/master/test/test_whoosh.py

8) : 命令行分词
-------------------

使用示例：`cat news.txt | python -m jieba > cut_result.txt`

命令行选项（翻译）：

    使用: python -m jieba [options] filename

    结巴命令行界面。

    固定参数:
      filename              输入文件

    可选参数:
      -h, --help            显示此帮助信息并退出
      -d [DELIM], --delimiter [DELIM]
                            使用 DELIM 分隔词语，而不是用默认的' / '。
                            若不指定 DELIM，则使用一个空格分隔。
      -D DICT, --dict DICT  使用 DICT 代替默认词典
      -u USER_DICT, --user-dict USER_DICT
                            使用 USER_DICT 作为附加词典，与默认词典或自定义词典配合使用
      -a, --cut-all         全模式分词
      -n, --no-hmm          不使用隐含马尔可夫模型
      -q, --quiet           不输出载入信息到 STDERR
      -V, --version         显示版本信息并退出

    如果没有指定文件名，则使用标准输入。

`--help` 选项输出：

    $> python -m jieba --help
    usage: python -m jieba [options] filename

    Jieba command line interface.

    positional arguments:
      filename              input file

    optional arguments:
      -h, --help            show this help message and exit
      -d [DELIM], --delimiter [DELIM]
                            use DELIM instead of ' / ' for word delimiter; or a
                            space if it is used without DELIM
      -D DICT, --dict DICT  use DICT as dictionary
      -u USER_DICT, --user-dict USER_DICT
                            use USER_DICT together with the default dictionary or
                            DICT (if specified)
      -a, --cut-all         full pattern cutting
      -n, --no-hmm          don't use the Hidden Markov Model
      -q, --quiet           don't print loading messages to stderr
      -V, --version         show program's version number and exit

    If no filename specified, use STDIN instead.

模块初始化机制的改变:lazy load （从0.28版本开始）
-------------------------------------------

jieba 采用延迟加载，"import jieba" 不会立即触发词典的加载，一旦有必要才开始加载词典构建前缀字典。如果你想手工初始 jieba，也可以手动初始化。

    import jieba
    jieba.initialize()  # 手动初始化（可选）


在 0.28 之前的版本是不能指定主词典的路径的，有了延迟加载机制后，你可以改变主词典的路径:

    jieba.set_dictionary('data/dict.txt.big')

例子： https://github.com/fxsjy/jieba/blob/master/test/test_change_dictpath.py

其他词典
========
1. 占用内存较小的词典文件
https://github.com/fxsjy/jieba/raw/master/extra_dict/dict.txt.small

2. 支持繁体分词更好的词典文件
https://github.com/fxsjy/jieba/raw/master/extra_dict/dict.txt.big

下载你所需要的词典，然后覆盖 jieba/dict.txt 即可；或者用 `jieba.set_dictionary('data/dict.txt.big')`

其他语言实现
==========

结巴分词 Java 版本
----------------
作者：piaolingxue
地址：https://github.com/huaban/jieba-analysis

结巴分词 C++ 版本
----------------
作者：yanyiwu
地址：https://github.com/aszxqw/cppjieba

结巴分词 Node.js 版本
----------------
作者：yanyiwu
地址：https://github.com/aszxqw/nodejieba

结巴分词 Erlang 版本
----------------
作者：falood
地址：https://github.com/falood/exjieba

结巴分词 R 版本
----------------
作者：qinwf
地址：https://github.com/qinwf/jiebaR

结巴分词 iOS 版本
----------------
作者：yanyiwu
地址：https://github.com/aszxqw/iosjieba

系统集成
========
1. Solr: https://github.com/sing1ee/jieba-solr

分词速度
=========
* 1.5 MB / Second in Full Mode
* 400 KB / Second in Default Mode
* 测试环境: Intel(R) Core(TM) i7-2600 CPU @ 3.4GHz；《围城》.txt

常见问题
=========
1. 模型的数据是如何生成的？https://github.com/fxsjy/jieba/issues/7
2. 这个库的授权是? https://github.com/fxsjy/jieba/issues/2

* 更多问题请点击：https://github.com/fxsjy/jieba/issues?sort=updated&state=closed

修订历史
==========
https://github.com/fxsjy/jieba/blob/master/Changelog

--------------------

jieba
========
"Jieba" (Chinese for "to stutter") Chinese text segmentation: built to be the best Python Chinese word segmentation module.

Features
========
* Support three types of segmentation mode:
* 1) Accurate Mode attempts to cut the sentence into the most accurate segmentations, which is suitable for text analysis.
* 2) Full Mode gets all the possible words from the sentence. Fast but not accurate.
* 3) Search Engine Mode, based on the Accurate Mode, attempts to cut long words into several short words, which can raise the recall rate. Suitable for search engines.

Usage
========
* Fully automatic installation: `easy_install jieba` or `pip install jieba`
* Semi-automatic installation: Download http://pypi.python.org/pypi/jieba/ , run `python setup.py install` after extracting.
* Manual installation: place the `jieba` directory in the current directory or python `site-packages` directory.
* `import jieba`.

Algorithm
========
* Based on a prefix dictionary structure to achieve efficient word graph scanning. Build a directed acyclic graph (DAG) for all possible word combinations.
* Use dynamic programming to find the most probable combination based on the word frequency.
* For unknown words, a HMM-based model is used with the Viterbi algorithm.

Main Functions
==============

1) : Cut
--------
* The `jieba.cut` function accepts three input parameters: the first parameter is the string to be cut; the second parameter is `cut_all`, controlling the cut mode; the third parameter is to control whether to use the Hidden Markov Model.
* `jieba.cut` returns an generator, from which you can use a `for` loop to get the segmentation result (in unicode), or `list(jieba.cut( ... ))` to create a list.
* `jieba.cut_for_search` accepts two parameter: the string to be cut; whether to use the Hidden Markov Model. This will cut the sentence into short words suitable for search engines.

**Code example: segmentation**

```python
#encoding=utf-8
import jieba

seg_list = jieba.cut("我来到北京清华大学", cut_all=True)
print "Full Mode:", "/ ".join(seg_list)  # 全模式

seg_list = jieba.cut("我来到北京清华大学", cut_all=False)
print "Default Mode:", "/ ".join(seg_list)  # 默认模式

seg_list = jieba.cut("他来到了网易杭研大厦")
print ", ".join(seg_list)

seg_list = jieba.cut_for_search("小明硕士毕业于中国科学院计算所，后在日本京都大学深造")  # 搜索引擎模式
print ", ".join(seg_list)
```

Output:

    [Full Mode]: 我/ 来到/ 北京/ 清华/ 清华大学/ 华大/ 大学

    [Accurate Mode]: 我/ 来到/ 北京/ 清华大学

    [Unknown Words Recognize] 他, 来到, 了, 网易, 杭研, 大厦    (In this case, "杭研" is not in the dictionary, but is identified by the Viterbi algorithm)

    [Search Engine Mode]： 小明, 硕士, 毕业, 于, 中国, 科学, 学院, 科学院, 中国科学院, 计算, 计算所, 后, 在, 日本, 京都, 大学, 日本京都大学, 深造


2) : Add a custom dictionary
----------------------------

* Developers can specify their own custom dictionary to be included in the jieba default dictionary. Jieba is able to identify new words, but adding your own new words can ensure a higher accuracy.
* Usage： `jieba.load_userdict(file_name) # file_name is the path of the custom dictionary`
* The dictionary format is the same as that of `analyse/idf.txt`: one word per line; each line is divided into two parts, the first is the word itself, the other is the word frequency, separated by a space
* Example：

        云计算 5
        李小福 2
        创新办 3

        [Before]： 李小福 / 是 / 创新 / 办 / 主任 / 也 / 是 / 云 / 计算 / 方面 / 的 / 专家 /

        [After]：　李小福 / 是 / 创新办 / 主任 / 也 / 是 / 云计算 / 方面 / 的 / 专家 /

3) : Keyword Extraction
-----------------------
* `jieba.analyse.extract_tags(sentence,topK,withWeight) # needs to first import jieba.analyse`
* `sentence`: the text to be extracted
* `topK`: return how many keywords with the highest TF/IDF weights. The default value is 20
* `withWeight`: whether return TF/IDF weights with the keywords. The default value is False

Example (keyword extraction)

https://github.com/fxsjy/jieba/blob/master/test/extract_tags.py

Developers can specify their own custom IDF corpus in jieba keyword extraction

* Usage： `jieba.analyse.set_idf_path(file_name) # file_name is the path for the custom corpus`
* Custom Corpus Sample：https://github.com/fxsjy/jieba/blob/master/extra_dict/idf.txt.big
* Sample Code：https://github.com/fxsjy/jieba/blob/master/test/extract_tags_idfpath.py

Developers can specify their own custom stop words corpus in jieba keyword extraction

* Usage： `jieba.analyse.set_stop_words(file_name) # file_name is the path for the custom corpus`
* Custom Corpus Sample：https://github.com/fxsjy/jieba/blob/master/extra_dict/stop_words.txt
* Sample Code：https://github.com/fxsjy/jieba/blob/master/test/extract_tags_stop_words.py

There's also a [TextRank](http://web.eecs.umich.edu/~mihalcea/papers/mihalcea.emnlp04.pdf) implementation available.

Use: `jieba.analyse.textrank(raw_text)`.

4) : Part of Speech Tagging
-----------
* Tags the POS of each word after segmentation, using labels compatible with ictclas.
* Example:

```pycon
>>> import jieba.posseg as pseg
>>> words = pseg.cut("我爱北京天安门")
>>> for w in words:
...    print w.word, w.flag
...
我 r
爱 v
北京 ns
天安门 ns
```

5) : Parallel Processing
-----------
* Principle: Split target text by line, assign the lines into multiple Python processes, and then merge the results, which is considerably faster.
* Based on the multiprocessing module of Python.
* Usage:
    * `jieba.enable_parallel(4)` # Enable parallel processing. The parameter is the number of processes.
    * `jieba.disable_parallel()` # Disable parallel processing.

* Example:
    https://github.com/fxsjy/jieba/blob/master/test/parallel/test_file.py

* Result: On a four-core 3.4GHz Linux machine, do accurate word segmentation on Complete Works of Jin Yong, and the speed reaches 1MB/s, which is 3.3 times faster than the single-process version.

6) : Tokenize: return words with position
----------------------------------
* The input must be unicode
* Default mode

```python
result = jieba.tokenize(u'永和服装饰品有限公司')
for tk in result:
    print "word %s\t\t start: %d \t\t end:%d" % (tk[0],tk[1],tk[2])
```

```
word 永和                start: 0                end:2
word 服装                start: 2                end:4
word 饰品                start: 4                end:6
word 有限公司            start: 6                end:10

```

* Search mode

```python
result = jieba.tokenize(u'永和服装饰品有限公司',mode='search')
for tk in result:
    print "word %s\t\t start: %d \t\t end:%d" % (tk[0],tk[1],tk[2])
```

```
word 永和                start: 0                end:2
word 服装                start: 2                end:4
word 饰品                start: 4                end:6
word 有限                start: 6                end:8
word 公司                start: 8                end:10
word 有限公司            start: 6                end:10
```


7) : ChineseAnalyzer for Whoosh
--------------------------------------------
* `from jieba.analyse import ChineseAnalyzer`
* Example: https://github.com/fxsjy/jieba/blob/master/test/test_whoosh.py

8) : Command Line Interface
-------------------

    $> python -m jieba --help
    usage: python -m jieba [options] filename

    Jieba command line interface.

    positional arguments:
      filename              input file

    optional arguments:
      -h, --help            show this help message and exit
      -d [DELIM], --delimiter [DELIM]
                            use DELIM instead of ' / ' for word delimiter; or a
                            space if it is used without DELIM
      -D DICT, --dict DICT  use DICT as dictionary
      -u USER_DICT, --user-dict USER_DICT
                            use USER_DICT together with the default dictionary or
                            DICT (if specified)
      -a, --cut-all         full pattern cutting
      -n, --no-hmm          don't use the Hidden Markov Model
      -q, --quiet           don't print loading messages to stderr
      -V, --version         show program's version number and exit

    If no filename specified, use STDIN instead.

Initialization
---------------
By default, Jieba don't build the prefix dictionary unless it's necessary. This takes 1-3 seconds, after which it is not initialized again. If you want to initialize Jieba manually, you can call:

    import jieba
    jieba.initialize()  # (optional)

You can also specify the dictionary (not supported before version 0.28) :

    jieba.set_dictionary('data/dict.txt.big')


Using Other Dictionaries
========
It is possible to use your own dictionary with Jieba, and there are also two dictionaries ready for download:

1. A smaller dictionary for a smaller memory footprint:
https://github.com/fxsjy/jieba/raw/master/extra_dict/dict.txt.small

2. There is also a bigger dictionary that has better support for traditional Chinese (繁體):
https://github.com/fxsjy/jieba/raw/master/extra_dict/dict.txt.big

By default, an in-between dictionary is used, called `dict.txt` and included in the distribution.

In either case, download the file you want, and then call `jieba.set_dictionary('data/dict.txt.big')` or just replace the existing `dict.txt`.

Segmentation speed
=========
* 1.5 MB / Second in Full Mode
* 400 KB / Second in Default Mode
* Test Env: Intel(R) Core(TM) i7-2600 CPU @ 3.4GHz；《围城》.txt

Online demo
=========
http://jiebademo.ap01.aws.af.cm/

(Powered by Appfog)
