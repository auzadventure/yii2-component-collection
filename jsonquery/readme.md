## JSON Query Creator

#### usage 
1. copy to /models folder
2. set your namespace
3. Add to your query

---

```
Table::find()
  ->andWhere(JsonQuery::objSearch($column,$key,$value))
```

#### objSearch($attr,$key,$value)

For `{key:value}` single object `{ key1 : value1, key2: value2 }`

<br><br><br>

#### aSearchValue($attr,$value)

For `array[obj obj]` for any `{ '?' : value }` 
Key is not important


<br><br><br>

#### aSearchKeyValue($attr,$key,$value)

For `array [obj,obj]` when you want a special `{ key : value }` pair
