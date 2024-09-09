# Product Requirements Document - プロダクト要求文書

## 1. Intro & Goal

このサービスでは、ユーザーはファンタジー世界での戦いをシミュレート出来ます。戦いの中で成長したり、レアなアイテムを見つけることで楽しむことが出来ます。 RPG のプリミティブな体験を得ることが出来ます。

また、他のプレイヤーと競い合うことで、闘争心を芽生えさせます。

このプロダクトは [Shibare PHP Framework](https://github.com/shibare-framework/) のリファレンス実装という側面も持ちます。このプロダクトを通して Shibare PHP Framework を成長させ、より現実的な実装を提供します。

1990 年代 ～ 2000 年代初頭に流行した CGI ゲームを模倣することで、シンプルな実装でフレームワークの要求を満たすことが出来ると考えます。

## 2. Concept / Value Proposition

このサービスは、かつて流行した CGI ゲームのクローンとして、 RPG のプリミティブな体験を得るための最小の実装を提供します。

エンドユーザーはブラウザでゲームを楽しむことができ、実装者は LAMP スタックをベースとしたリファレンスとしてこのリポジトリを参照することが出来ます。

また、仕様書や設計書などのドキュメントも同一のリポジトリで提供することで、要求定義から実装まで、ゲーム開発の全ての段階をこのリファレンス実装から得ることが出来ます。プロによるゲーム開発の一連の流れをこのリポジトリを参照することで誰でも確認出来るようになります。このプロダクト要求文書が最初の一歩です。

## 3. Product Vision

このサービスを通して、エンドユーザーに遊びの場所を提供し、エンジニアに遊びの場所の提供の仕方を例示します。

## 4. Who's it for?

1. ブラウザでプリミティブな RPG の体験を得たいエンドユーザー。
2. LAMP ベースのゲーム開発で必要な文書や実装の流れを知りたいエンジニア。

## 5. Why build it?

- 自作フレームワークを実践に落とし込むため。
- これまでの自分のエンジニア知識を具体化させるため。

## 6. What is it?

RPG のシミュレーションを行うことが出来る Web サービス。

### 6-1. Glossary

- ユーザーアカウント
- 名前
- レベル
- 経験値
- 所持金
- 職業
- 基礎能力
- 攻撃力
- 防御力
- クリティカル率
- ターン制戦闘
- 闘技場
- 武器
- 防具
- アクセサリ
- レア度
- 連勝回数
- クールタイム
- ランキング

### 6-2. User Types

- エンドユーザー
- エンジニア

### 6-3. UI/Screens/Functionalities

## 7. Brainstormed Ideas

## 8. Competitors & Product Inspiration

- [ゲームの缶詰 - FF ADVENTURE+ | FFアドベンチャー | FFA+](http://www.game-can.com/ffa/)
- [あるけみすと](https://games-alchemist.com/)

## 9. Seeding Users & Content

## 10. Mockups

## 11. Tech Notes

64bit unsigned integer で足りないくらいインフレさせても面白そうなので、ステータスは varchar で管理した方がいいかもしれない。

## 12. References

- [初めて書くPRD（プロダクト要求仕様書）｜Miz Kushida](https://note.com/miz_kushida/n/n7e35a2a2b370)
- [FFA+Wiki - atwiki（アットウィキ）](https://w.atwiki.jp/game_can_ffa/)
- [FF ADVENTURE - Enpedia](https://enpedia.rxy.jp/wiki/FF_ADVENTURE)
- [昔懐かしい最新CGIゲーム　「あるけみすと」を遊んだ感想｜あけお](https://note.com/kind_phlox918/n/na0d0e0869b05)
